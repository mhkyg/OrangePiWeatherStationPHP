<?
class Sensor_Factory{
  private $config,$instances;
	function __construct(Sensor_Config $config){
	 $this->config = $config; 	
	}
  /**
   return subclass of Abstract_Sensor
   */
  function getInstance($name){
    if (!isset($this->instances[$name]) ) {
    	$confdata = $this->config->getConfigData();
      if (!is_a($confdata[$name]["class"],"Abstract_Sensor" )) {
      	throw new Exception("Class NOT an Abstract_Sensor") ;
      }
      $this->instances[$name] = new $confdata[$name]["class"]($confdata[$name]["params"]);
    }
    return $this->instances[$name];
  }
  /**
   return array([sensor_name]=>[sensor_object])
   */
  function enumerateSensors(){
    $result = array();
    $confdata = $this->config->getConfiguredSensors();
  	foreach ($confdata as $sensor_name) {
   	  $result[$sensor_name] = $this->getInstance($sensor_name);
    }
    return $result;
  }
  
}