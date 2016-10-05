	<?php
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
	 * Get the key from a line of a WMIC query that is formatted as a list
	 * @param  array(String) 	$wmicOutput 	The output from the WMIC query
	 * @param  int 				$lineNumber 	which line number is required (starting from 0)
	 * @return String  							The WMIC key
	 */
	function getWmicKeyFromList($wmicOutput, $lineNumber) {
		$key = substr($wmicOutput[$lineNumber], 0, strpos($wmicOutput[$lineNumber], '='));
		return($key);
	}

	function getWmicArrayUniqueKey($wmicOutput) {
		$output = array();
		for($i=0; $i<count($wmicOutput); $i++) {
			if ($wmicOutput[$i] != '') {
				$key = substr($wmicOutput[$i], 0, strpos($wmicOutput[$i], '='));
				$value = substr($wmicOutput[$i], strpos($wmicOutput[$i], '=') + 1);
				$output[$key] = $value;
			}
		} 
		return $output;
	}

	function getNicInfo($hostname) {
		set_time_limit(300);
		exec(escapeshellcmd("wmic /node:$hostname product get name /format:list"), $output, $result);
		print_r(getWmicArrayCommonKey($output));
		//return array($output, $result);
	}

	function getWmicArrayCommonKey($wmicOutput) {
		$output = array();
		for($i=0; $i<count($wmicOutput); $i++) {
			if ($wmicOutput[$i] != '') {
				$key = substr($wmicOutput[$i], 0, strpos($wmicOutput[$i], '='));
				$value = substr($wmicOutput[$i], strpos($wmicOutput[$i], '=') + 1);
				if ($value != '') {
					$output[$key][] = $value;
				}
			}
		}
		return $output;
	}

	/**
	 * Get the installed OS name, version and country code
	 * @param  String $hostname The hostname of the target machine
	 * @return array           	An array containing an array of key value pairs and the result of the WMIC query
	 */
	function getOsDetails($hostname) {
		exec(escapeshellcmd("wmic /node:$hostname os get version, caption, countryCode /format:list"), $output, $result);
		if ($result == 0) {
			return array(getWmicArrayUniqueKey($output), $result);
		} 
		return array($output, $result);
	}
	/**
	 *uses wmic to return a list of the installed software on the supplied hostname
	 *the first element of the $output array will be the word name
	 * will need set_time_limit(300) using as the command takes a while.
	 * @param  String $hostname
	 * @return array(String,String)
	 */
	function getSoftware($hostname) {
		set_time_limit(300);
		exec(escapeshellcmd("wmic /node:$hostname product get name"), $output, $result);
		return array($output, $result);
	}

	/**
	 * Get the BIOS version and serial number
	 * @param  String $hostname The hostname of the target machine
	 * @return array           	An array containing an array of key value pairs and the result of the WMIC query
	 */
	function getBiosDetails($hostname) {
		exec(escapeshellcmd("wmic /node:$hostname bios get version, serialnumber /format:list"), $output, $result);
		if ($result ==0) {
			return array(getWmicArrayUniqueKey($output), $result);
		}
		return array($output, $result);
	}
	?>