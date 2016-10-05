<?php 

	class SoftwareLibrary implements JsonSerializable {

		public function __construct($name) {
			$this->name = $name;
			$this->software = [];
		}
		public function getName() {
			return $this->name;
		}
		public function addSoftware($software, $hash) {
			$this->software[$hash] = $software;
		}
		public function getSoftware($hash){
			return $this->software[$hash];
		}
		public function getAllSoftware(){
			return $this->software;
		}
		public function removeSoftware($software) {
			array_splice($this->software, $software, 1); 
		}
		public function sortSoftware() {
			uasort($this->software, [$this, "softwareArraySort"]);
		}
		private function softwareArraySort($a, $b) {
			return strcmp($a->getName(), $b->getname());
		}
		public function toArray() {
			return [
				'name'=>$this->name,
				'software'=>$this->$software
			];
		}
		public function jsonSerialize() {
			return($this->toArray());
		}
		public function containsSoftware($hash) {
			return isset($this->software[$hash]);
			//return array_key_exist($hash, $this->software);
		}
	}
?>