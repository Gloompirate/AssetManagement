<?php
/**
 *  Maybe add some magic methods?
 */
	class Computer implements JsonSerializable {
		//other properties? scanDate,OU, product number, carepack serial,
		private $name,$ipAddress,$location,$lastUser,$osName,$osVersion,$osCountryCode,$biosVersion,$serialNumber,$manufacturer,$model,$numberProcessors,$totalMemory;
		private $status, $scannedHardware, $scannedSoftware = 0;
		private $installedSoftware = [];


		public function __construct($name) {
			$this->name = $name;
		}
		public function getName() {
			return $this->name;
		}
		public function setIpAddress($ipAddress) {
			$this->ipAddress = $ipAddress;
		}
		public function getIpAddress() {
			return $this->ipAddress;
		}
		public function setLocation($location) {
			$this->location = $location;
		}
		public function getLocation() {
			return $this->location;
		}
		public function setLastUser($lastUser) {
			$this->lastUser = $lastUser;
		}
		public function getLastUser() {
			return $this->lastUser;
		}
		public function setStatus($status) {
			$this->status = $status;
		}
		public function getStatus() {
			return $this->status;
		}
		public function setOsName($osName) {
			$this->osName = $osName;
		}
		public function getOsName() {
			return $this->osName;
		}
		public function setOsVersion($osVersion) {
			$this->osVersion = $osVersion;
		}
		public function getOsVersion() {
			return $this->osVersion;
		}
		public function setOsCountryCode($osCountryCode) {
			$this->osCountryCode = $osCountryCode;
		}
		public function getOsCountryCode() {
			return $this->osCountryCode;
		}
		public function setBiosVersion($biosVersion) {
			$this->biosVersion = $biosVersion;
		}
		public function getBiosVersion() {
			return $this->biosVersion;
		}
		public function setSerialNumber($serialNumber) {
			$this->serialNumber = $serialNumber;
		}
		public function getSerialNumber() {
			return $this->serialNumber;
		}
		public function setManufacturer($manufacturer) {
			$this->manufacturer = $manufacturer;
		}
		public function getManufacturer() {
			return $this->manufacturer;
		}
		public function setModel($model) {
			$this->model = $model;
		}
		public function getModel() {
			return $this->model;
		}
		public function setNumberProcessors($numberProcessors) {
			$this->numberProcessors = $numberProcessors;
		}
		public function getNumberProcessors() {
			return $this->numberProcessors;
		}
		public function setTotalMemory($totalMemory) {
			$this->totalMemory = $totalMemory;
		}
		public function getTotalMemory() {
			return $this->totalMemory;
		}
		public function setScannedHardware($scannedHardware) {
			$this->scannedHardware = $scannedHardware;
		}
		public function getScannedHardware() {
			return $this->scannedHardware;
		}
		public function setScannedSoftware($scannedSoftware) {
			$this->scannedSoftware = $scannedSoftware;
		}
		public function getScannedSoftware() {
			return $this->scannedSoftware;
		}
		public function getInstalledSoftware() {
			return $this->installedSoftware;
		}
		public function addInstalledSoftware($software, $hash) {
			$this->installedSoftware[$hash] = $software;
		}
		public function removeInstalledSoftware($software) {
			array_splice($this->installedSoftware, $software, 1); 
		}
		public function removeAllInstalledSoftware() {
			$this->installedSoftware = [];
		}
		public function sortInstalledSoftware() {
			uasort($this->installedSoftware, [$this, "softwareArraySort"]);
		}
		private function softwareArraySort($a, $b) {
			return strcmp($a->getName(), $b->getname());
		}
		public function toArray() {
			return [
			    'name'=>$this->name,
			   	'status'=>$this->status,
			    'ipAddress'=>$this->ipAddress,
			    'location'=>$this->location,
			    'lastUser'=>$this->lastUser,
			    'status'=>$this->status,
			    'osName'=>$this->osName,
			    'osVersion'=>$this->osVersion,
			    'osCountryCode'=>$this->osCountryCode,
			    'biosVersion'=>$this->biosVersion,
			    'serialNumber'=>$this->serialNumber,
			    'manufacturer'=>$this->manufacturer,
			    'model'=>$this->model,
			    'numberProcessors'=>$this->numberProcessors,
			    'totalMemory'=>$this->totalMemory,
			    'installedSoftware'=>$this->installedSoftware
			];
		}
		public function jsonSerialize() {
			return($this->toArray());
		}
	}
?>