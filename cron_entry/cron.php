<?
require_once __DIR__."/../application/model/auto_loader.php";

class Cron_Runner{
  private $config;
	function __construct(Sensor_Config $config ,Weather_Data_Handler $data_handler){
		$this->config = $config; 
    $this->data_handler = $data_handler;
	}
  
  function run(){
  	$sensor_factory = new Sensor_Factory($this->config);
    $sensors = $sensor_factory->enumerateSensors();
    foreach ($sensors as $sensor_name=>$sensor) {
    	$data = $sensor->getData();
      foreach ($data as $name=>$mesures) {
        foreach ($mesures as $mesurement_time_timestamp=>$data) {
        	$this->data_handler->saveData(array("name"=>$name."::".$data["unit"],"value"=>$data["value"],"timestamp"=>$mesurement_time_timestamp ));
        }
      }
    }
  }
}
$db_factory = new Database_Factory(new Database_Config() );
$database = $db_factory->getInstance();
$runner = Cron_Runner(new Sensor_Config(),new Weather_Data_Handler($database) );
$runner->run();