<?
/**
 edit provided c line 17 for i2c bus number
 edit provided c file line 24 for i2c address
 compile provided c library
 gcc TSL2561.c -o TSL2561
 
 */
class TSL2561 extends Abstract_Sensor{
  /*private $bus, $address;
 	function __construct($bus,$address){
 		$this->bus =  $bus;
    $this->address = $address;
 	}  */
  
  function getData(){
  	
    $location = $this->getParams()["tsl_reader_location"]   ;
    $parts = explode(";",$res = shell_exec($location));
    if (count($parts)<2 ) {
    	Throw new Exception("sensor read failed");
    }
    $ch0 = $parts[0];
    $ch1 = $parts[1]; 
    $rate = $ch1/$ch0; 
    switch ($rate) {
    case $rate<0.52:
      $lux =  0.0315 * $ch0 -  0.0593 * $ch0 * pow($rate, 1.4);
    	break;
    case $rate<0.65:
      $lux =  0.0229 * $ch0 -  0.0291 * $ch0 ;
    	break;
    case $rate<0.80:
      $lux =  0.0157 * $ch0 -  0.0180 * $ch0 ;
    	break;
    case $rate<0.13:
      $lux =  0.00338 * $ch0 -  0.00260 * $ch0 ;
    	break;
    case $rate>0.13:
      $lux =  0 ;
    	break;      
    default:
    	
    	break;
    }
    return array("luminosity"=>array(mktime()=>array("value"=>$lux,"unit"=>"lux") )) ;
  }
  
 }