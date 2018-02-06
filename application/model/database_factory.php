<?
class Database_Factory{
  private $database = false;
	function __construct(Database_Config $database_config){
	  $this->database_config = $database_config;	
	}
  
  
  function getInstance(){
    if ($this->database === false ) {
    	try{
        $this->database = new TheDatabase_MySQLi();
        $this->database->connect($this->database_config["user"]
                          ,$this->database_config["password"] 
                          ,$this->database_config["host"] 
                          ,$this->database_config["database"] );
        $this->database->setEncoding();
      }catch(Exception $e){
        die("Database Error");
      }
    	
    }
    return $this->database;
  }
  
}

  