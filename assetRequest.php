<?php
	require_once('computer.php');
	require_once('software.php');
	require_once('softwarelibrary.php');

	//findout if the information is already stored (this will be db later but just use files for now)
	
	if (isset($_GET['name'])){
		$computerName = strtoupper($_GET['name']);
		$filepath = 'Data/';
		$computer;
		//findout if the information is already stored (this will be db later but just use files for now)
		//if it is load the data, else create a new object
		if (file_exists($filepath . $computerName . '.txt')) {
			$computer = unserialize(file_get_contents($filepath . $computerName . '.txt'));
		} else {
			$computer = new Computer($computerName);
		}
		//find out if the computer is online
		list($output, $result, $ip) = pingHost($computer->getName());

		if (!$result) { //computer is online
			$computer->setStatus(1);
			$computer->setIpAddress($ip);
			//get the username
			list($output, $result) = getUser($computer->getName());
			if (!$result) { //found a user
				$computer->setLastUser($output);
			} else $computer->setLastUser('Error getting username');
		} else { //computer is offline
			$computer->setStatus(0);
			$computer->setIpAddress('Offline');
		}
		//if we want the hardware info, or the computer hardware has never been scanned
		if ($computer->getStatus() && (!$computer->getScannedHardware() || (isset($_GET['hardware'])))) {
			//get OS details
			list($output, $result) = getOsDetailsCsv($computer->getName());
			if (!$result) {
				$computer->setOsName($output[1]); 
				$computer->setOsCountryCode($output[2]); 
				$computer->setOsVersion($output[3]);
			}
			//get BIOS details
			list($output, $result) = getBiosDetailsCsv($computer->getName());
			if (!$result) {
				$computer->setSerialNumber($output[1]);
				$computer->setBiosVersion($output[2]);
			}
			//get hardware details
			list($output, $result) = getComputerDetailsCsv($computer->getName());
			if (!$result) {
				$computer->setManufacturer($output[1]);
				$computer->setModel($output[2]);
				$computer->setNumberProcessors($output[3]);
				$computer->setTotalMemory($output[4]);
			}
			$computer->setScannedHardware(1);
		}
		//if we want the software info
		if(isset($_GET['software'])  && $computer->getStatus()) {
			//load or create software library
			if (file_exists($filepath . 'swlibrary.txt')) {
				$swlibrary = unserialize(file_get_contents($filepath . 'swlibrary.txt'));
			} else {
				$swlibrary = new SoftwareLibrary('library');
			}
			//set the php execution limit as getting software takes time
			set_time_limit(300);
			//get the software details
			list($output, $result) = getSoftwareCsv($computer->getName()); 
			if($result == 0) {
				for ($i = 0; $i<count($output); $i++) {
					if ($output[$i][1]) {
						$software = new Software(utf8_encode($output[$i][1]),$output[$i][3]);
						$software->setVendor(utf8_encode($output[$i][2]));
						//if the software is not in the software library, add it, and then add to the computer
						//if it is in the software library add that one to the computer
						if(!$swlibrary->containsSoftware($software->getHash())){
							$swlibrary->addSoftware($software,$software->getHash());
							$computer->addInstalledSoftware($software, $software->getHash());
						} else {
							$computer->addInstalledSoftware($swlibrary->getSoftware($software->getHash()),$software->getHash());
						}
					}
				}
				//sort the software and library by software name alphabetically
				$computer->sortInstalledSoftware();
				$swlibrary->sortSoftware();
				unset($software);
			}
			$computer->setScannedSoftware(1);
			//write the software library data back to file (db once finished)
			$file = fopen($filepath . 'swlibrary.txt', 'w') or die('Unable to open file!');
			fwrite($file, serialize($swlibrary)); 
			fclose($file);
			unset($swlibrary);
		}
		if ($computer->getStatus()) {
			//write the computer data back to file (db once finished)
			$file = fopen($filepath . $computer->getName() . '.txt', 'w') or die('Unable to open file!');
			fwrite($file, serialize($computer)); 
			fclose($file);
		}
		//return the data as JSON
		echo json_encode($computer);
		unset($computer);
	}


	/**
	 * Ping the supplied hostname and return the output and result
	 * @param  String $hostname contains the hostname
	 * @return array(String, String, String) 
	 */
	function pingHost($hostname) {
		exec(escapeshellcmd("ping -n 1 -4 $hostname"), $output, $result);
		if($result == 0) {
			$start = strpos($output[1], '[') +1;
			$end = strpos($output[1], ']') - $start;
			$ip =  substr($output[1], $start, $end);
		} else {
			$ip = 'Offline';
		}
		return [$output, $result, $ip];
	}
	
	/**
	 * Get the IP Address from the 2nd line of text returned by the ping command
	 * @param  String $ipstring the string contatining the ip address
	 * @return String
	 */
	function getIp($ipString) {
		$start = strpos($ipString, '[') +1;
		$end = strpos($ipString, ']') - $start;
		return substr($ipString, $start, $end);
	}

	/**
	 * Find the current user via WMIC
	 * @param  String 	$hostname The hostname of the target machine
	 * @return array(String, String)
	 */
	function getUser($hostname) {
		$hostname = checkHostnameForDashes($hostname); 
		exec(escapeshellcmd("wmic /node:$hostname computersystem get username /format:list"), $output, $result);
		if ($result == 0) {
			return [substr($output[2], strpos($output[2], '=') + 1), $result];
			//return [getWmicValueFromList($output,2), $result];
		} else return [$output, $result];
	}

	/**
	 * Get the value from a line of a WMIC query that is formatted as a list
	 * @param  array(String) 	$wmicOutput 	The output from the WMIC query
	 * @param  int 				$lineNumber 	which line number is required (starting from 0)
	 * @return String  							The WMIC value
	 */
	function getWmicValueFromList($wmicOutput, $lineNumber) {
		$value = substr($wmicOutput[$lineNumber], strpos($wmicOutput[$lineNumber], '=') + 1);
		return($value);
	}

	/**
	 * Get all software installed via MSI
	 * @param  String $hostname The hostname of the target machine
	 * @return array          An array containing all the software, and the result of the WMIC query.
	 */
	function getSoftwareCsv($hostname) {
		$hostname = checkHostnameForDashes($hostname); 
		exec(escapeshellcmd("wmic /node:$hostname product get name, vendor, version /format:csv"), $output, $result);
		if (!$result) {
			$return = [];
			for ($i = 2; $i<count($output); $i++) {
				$return[] = explode(',', $output[$i]);
			}
			return [$return, $result];
		} else return [$output, $result];
	}

	/**
	 * Get the OS details
	 * @param  String $hostname The hostname of the target machine
	 * @return array           	An array containing an array of values and the result of the WMIC query
	 */
	function getOsDetailsCsv($hostname) {
		$hostname = checkHostnameForDashes($hostname); 
		exec(escapeshellcmd("wmic /node:$hostname os get version, caption, countryCode /format:csv"), $output, $result);
		if ($result == 0) {
			return [explode(',', $output[2]), $result];
		} 
		return [$output, $result];
	}

	/**
	 * Get the BIOS version and serial number
	 * @param  String $hostname The hostname of the target machine
	 * @return array           	An array containing an array of values and the result of the WMIC query
	 */
	function getBiosDetailsCsv($hostname) {
		$hostname = checkHostnameForDashes($hostname); 
		exec(escapeshellcmd("wmic /node:$hostname bios get version, serialnumber /format:csv"), $output, $result);
		if ($result ==0) {
			return [explode(',', $output[2]), $result];
		}
		return [$output, $result];
	}

	/**
	 * Get information about the computer
	 * @param  String $hostname The hostname of the target machine
	 * @return array           	An array containing an array of values and the result of the WMIC query
	 */
	function getComputerDetailsCsv($hostname) {
		$hostname = checkHostnameForDashes($hostname); 
		exec(escapeshellcmd("wmic /node:$hostname computersystem get Manufacturer, Model, NumberofProcessors, totalphysicalmemory, username /format:csv"), $output, $result);
		if ($result ==0) {
			return [explode(',', $output[2]), $result];
		}
		return [$output, $result];
	}

	/**
	 * If the hostname contains dashes encase it in quotes so WMIC works correctly
	 * @param  String $hostname the hostname
	 * @return String           the hostname encased in quotes if necessary
	 */
	function checkHostnameForDashes($hostname) {
		if (strpos($hostname, '-') !== false) {
		    $hostname = '"' . $hostname . '"';
		}
		return $hostname;
	}
?>