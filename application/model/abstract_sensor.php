<?
abstract class Abstract_Sensor{
  private $params;
	final function __construct($params){
		$this->params = $params;
	}
  final protected getParams(){
    return $this->params;
  }
  /**
   return array("name"=>array([mesurement_time_timestamp]=>array("value"=>decimal,"unit"=>(string)),.. );
   */
  abstract function getData();
}