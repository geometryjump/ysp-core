<?php
/*

by nobdod
vk.com/nobdod
github.com/nobdod

*/
class ClockParser
{
	private $debug=false;
	private $installer = array('sec','min','hour','day','mouth','year');
	private $installer_time = array('1','60','3600','86400','2592000','31104000');
	/*function __construct(){
	}*/
	public function parse($string){
		$this->debug("getInstaller(".$string.")");
		$type = $this->getInstaller($string);
		$type_n = $this->installer[$type];
		$this->debug("type(".$type.");$type_n(".$type_n.")");
		if(empty($type_n)){
			throw new Exception("Type is empty!", 1);
		}
		$this->debug("methoding(".$type.",".$string.")");
		return $this->methoding($type,$string);
	}
	private function getInstaller($string){
		$return = 0;
		$string = preg_replace("/[0-9]/", '', $string);
		for($i = 0; $i < count($this->installer); $i++){
			if($string == $this->installer[$i]){
				$return = $i;
				$i = 50000;//fix error
			}
		}
		return $return;
	}
	private function methoding($type,$string){
		$type_n = $this->installer[$type];
		$string = preg_replace("/[^0-9]/", '', $string);
		if(empty($type_n)){
			throw new Exception("Type is empty!", 1);
		}		
		if(empty($string)){
			throw new Exception("String is empty!", 1);
		}
		if(!is_numeric($string)){
			throw new Exception("String not a numeric!", 1);
		}
		return $this->installer_time[$type] * $string;
	}
	private function debug($string){if($this->debug){echo sprintf("%s<br/>",$string);}}
}
$d = new ClockParser();
echo 
	  $d->parse("1hour");
?>