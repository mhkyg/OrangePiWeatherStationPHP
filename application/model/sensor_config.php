<?
class Sensor_Config extends Abstract_Sensor_Config{
  /**
   array("name"=>array("class"=>[class]
                      ,"params"=>array() )
   **/
  protected  $sensors = array(
    "BMP280"=>array("class"=>"System_Call_Sensor"
                    "params"=>array("command_to_run"=>__DIR__."../../source/bme280_p"
                                    "return_name"=>"pressure"
                                    "return_unit"=>"Pa"
                                    )
    )
    "inside_dht"=>array("class"=>"DHT22"
                      ,"params"=>array("port_number"=>15)
                      )
    ,"luminosity"=>array("class"=>"TSL2561"
                        ,"params"=>array("tsl_reader_location"=>__DIR__."../../source/TSL2561")
                      )                  
  );
  
  function getConfigData(){
  	return $this->sensors;
  }
  
  function getConfiguredSensors(){
  	return array_keys($this->sensors);
  }
	
}