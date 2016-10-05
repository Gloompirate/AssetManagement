<?php  

	class Software implements JsonSerializable {

		public function __construct($name, $version = '0', $type = 'Standard', $visibility = 'Visible') {
			$this->name = $name;
			$this->version = $version;
			$this->hash = md5($name . $version);
			$this->type = $type;
			$this->visibility = $visibility;
			$this->vendor = '';
		}
		/**array  array containing [property name, value, default value,type, display options]
		*default value = the default value
		*type = text == 0, option == 1, list == 2, table == 3
		*display options = visible == 0, hidden == 1
		*
		public function getPropertiesJSON() {
			return json_encode([['name', $this->name, 'name', 0, 0], ['version', $this->version, '0', 0, 0], ['vendor', $this->vendor, 'Some Guy',0, 0], ['type', $this->type, 0, 0, 0,], ['visibility', $this->visibility, 0, 0, 0], ['hash', $this->hash, 'hash', 0, 1]]);
		}
		public function getProperties() {
			return [['Name', $this->name, 0, 0], ['Version', $this->version, 0, 0], ['Vendor', $this->vendor, 0, 0], ['Type', $this->type, 0, 0,], ['Visibility', $this->visibility, 0, 0], ['Hash', $this->hash, 0, 1]];
		}
		public function update() {
			$properties = $this->getProperties();
			for($i=0; $i<count($properties); $i++) {
				if(!property_exists($this, strtolower($properties[$i][0]))) {
					$this->$properties[$i][0] = $properties[$i][2];
				}
			}
			//array_diff();//do this tommorrow);
		}*/
		//needs editing if dynamic stuff happens
		public function toArray() {
			return [
			    'name'=>$this->name,
			   	'version'=>$this->version,
			    'vendor'=>$this->vendor,
			    'type'=>$this->type,
			    'visibility'=>$this->visibility,
			    'hash'=>$this->hash
			];
		}
		/*super alpha version
		public function toArray(){
			$returnProperties = [];
			$classProperties = $this->getProperties();
			for($i=0; $i<count($classProperties); $i++) {
				$returnProperties[$classProperties[$i][0]] = $classProperties[$i][1];
			}
			return $returnProperties;
		}*/
		public function getName() {
			return $this->name;
		}
		public function getHash() {
			return $this->hash;
		}
		public function setVersion($version) {
			$this->version = $version;
		}
		public function getVersion() {
			return $this->version;
		}
		public function setVendor($vendor) {
			$this->vendor = $vendor;
		}
		public function getVendor() {
			return $this->vendor;
		}
		public function setType($type) {
			$this->type = $type;
		}
		public function getType() {
			return $this->type;
		}
		public function setVisibility($visibility) {
			$this->visibility = $visibility;
		}
		public function getVisibility() {
			return $this->visibility;
		}
		public function jsonSerialize() {
			return($this->toArray());
		}

	}
?>