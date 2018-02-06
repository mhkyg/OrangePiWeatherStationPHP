<?
class System_Call_Sensor extends Abstract_Sensor{

  function getData(){
    $params = $this->getParams();
  	$result = shell_exec($params["command_to_run"]);
    return array($params["return_name"]=>array("value"=>$result,"unit"=>$params["return_unit"]);
  }
  
}