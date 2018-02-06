<?
/**
 edit provided c line 26 for i2c bus number
 edit provided c file line 33 for i2c address
 compile provided c library
 gcc  -g -Wall -Wextra -pedantic -std=c11 -o bme280_p bme280_p.c
 
 */
class BMP280 extends Abstract_Sensor{
  /*private $bus, $address;
 	function __construct($bus,$address){
 		$this->bus =  $bus;
    $this->address = $address;
 	}  */
  
  protected function getPressure(){
    $location = $this->getParams()["bmp_reader_location"]   ;
    $parts = explode(";",$res = shell_exec($location));
    if (count($parts)<2 ) {
    	Throw new Exception("sensor read failed");
    }    
    return array("temp"=>$parts[1],"pressure"=>$parts[0],"timestamp"=>mktime() ) ;
  }
  function getData(){

    $raw_data = $this->getPressure();



    return array("pressure"=>array($raw_data["timestamp"]=>array("value"=>$raw_data["pressure"],"unit"=>"hPa") )
                ,"temperature"=>array($raw_data["timestamp"]=>array("value"=>$raw_data["temp"],"unit"=>"Celsius") ));
    );
  }
 }