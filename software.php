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