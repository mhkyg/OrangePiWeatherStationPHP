<?
function auto_load($class_name){
  switch ($class_name) {
  case "PHP_GPIO":
    require_once __DIR__.'/../model/gpio.php';
  break;    
  case "Weather_Abstract_Page":
    require_once __DIR__.'/../view/abstract_page_template.php';
  break;
  case "Weather_Main_Template":
    require_once __DIR__.'/../view/template.php';
  break;
  case "Current_Weather_Viewer":
    require_once __DIR__.'/../view/current_temperature_viewer.php';
  break;
  case "Weather_Statistics_Viewer":
    require_once __DIR__.'/../view/statistics_viewer.php';
  break;  
  case "Weather_Timeline_Viewer":
    require_once __DIR__.'/../view/timeline_viewer.php';
  break;
  case "Weather_Data_Handler":
    require_once __DIR__."/../model/data_handler.php";
  break;
  case "TheDatabase_MySQLi":
    require_once __DIR__.'/../model/database.php';
  break;
  case "PmNumber":
    require_once __DIR__.'/../model/pmnumber.php';
  break;
  case "CPU_Info":
    require_once __DIR__.'/../model/cpu_info.php';
  break;  
  case "Database_Config":
    require_once __DIR__.'/../model/database_config.php';
  break;  
  case "Database_Factory":
    require_once __DIR__.'/../model/database_factory.php';
  break;      
  case "Absolute_Humidity_Util":
    require_once __DIR__.'/../model/absolute_humidity_util.php';
  break;   
  case "LCD_Text_Generator":
    require_once __DIR__.'/../model/lcd_text_generator.php';
  break;   
  
  case "BMP280":
    require_once __DIR__.'/../model/BMP280.php';
  break;   
  
  
  
  default:
  	//throw new Exception("Class not found");
  	break;
  }
	
  
  
  
}
spl_autoload_register('auto_load');
