<?
class DHT22_Sensor extends Abstract_Sensor{

  function getData(){
  	$gpio =  new PHP_GPIO("/sys/class/gpio");
    
    $dht22 = new PHP_DHT22($gpio,$this->params["port_number"]);
    
    $res = $dht22->getTemperatureAndHumidity();
    
    
    return array("temperature"=>array("value"=>$res["temperature"],"unit"=>"C")
                ,"humidity"=>array("value"=>$res["humidity"],"unit"=>"%")
                ) ;
    
  }
}