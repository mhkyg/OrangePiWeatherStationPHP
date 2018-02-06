<?

require_once __DIR__.'/../application/model/auto_loader.php';


$db_factory = new Database_Factory(new Database_Config() );
$database = $db_factory->getInstance();
$data_handler = new Weather_Data_Handler($database);

switch ($_GET["page"]) {
  case "timeline":
    $oldal = new Temperature_Timeline_Viewer($data_handler,$_POST);	
  	break;
  case "statistics":
    $oldal = new Temperature_Statistics_Viewer($data_handler);
  	break;  
  default:
    $oldal = new Current_Temperature_Viewere(__DIR__);	
	break;
}


$template = new Temperature_Main_Template($oldal);
$template->show();