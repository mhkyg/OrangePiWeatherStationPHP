<?
class Dummy_Sensor extends Abstract_Sensor{

  /**
   return array("name"=>array([mesurement_time_timestamp]=>array("value"=>decimal,"unit"=>(string)),.. );
   Since this class is for testing it returns one day data and it'll look like a sinus...
   */
  final function getData(){
    $day_start = strtotime("midnight");
    $day_end = strtotime("+1 day midnight");
    $day_seconds = $day_end - $day_start; 
    $result = array();
    for ($i=0;$i+=300 ;$i<$day_seconds ) {
      $timestamp = $day_start+$i;
      $float = sin(deg2rad($i*360/$day_seconds));
    	$result["temperature"][$timestamp] = array("value"=>$float*10+25,"unit"=>"°C") ;
      $result["humidity"][$timestamp] = array("value"=>$float*20+40,"unit"=>"%")  ;
    }
    return $result; 
  }
}