<?
class Weather_Data_Handler{
  const min_data_save_interval_seconds = 600;
  private $type_ids = false;
	function __construct($database){
		$this->database = $database;
	}
  /**
   * data: array("type"=>[(string) type],"value"=>[(decimal) value],"timestamp" )
   * return  true on succesfull insert or false on failure
   */
  function saveData(array $data){
    $fp = fopen(__DIR__."/lock.txt", 'r+');
    while(!flock($fp, LOCK_EX | LOCK_NB) ) {
       usleep(100);
       if ($count++ > 10) return false;
    }
    
    $sql="SELECT * FROM `weather_data` WHERE `timestamp` > ".$this->database->getEscaped($data["timestamp"] - self::min_data_save_interval_seconds  )." AND type=".$this->database->getEscaped($this->getTypeID($data["type"]) );
    //echo $sql;
    $db=$this->database;
    $db->setQuery($sql);
    $res=$db->loadAssocList();   
   
    if (!empty($res) ) {
    	return false;
    } 
   
    $sql = "INSERT INTO `weather_data` (`id`, `timestamp`, `type`,`value`) 
                VALUES (NULL
                        , ".$this->database->getEscaped($data["timestamp"])."
                        , ".$this->database->getEscaped($this->getTypeID($data["type"]) )."
                        , ".$this->database->getEscaped($data["value"])."
                        );";
    //echo $sql;
  	$db->setQuery($sql);
    $db->query();

    

    
    
    fclose($fp);        
    return (bool)$res;  	
  }
  /**
   *$select = all | min | max | avg  ;
   *$type = * | [any type] ( *= all types)
   */
   
  function queryData($select = "all",$type = "*",$timestamp_from = null, $timestamp_to = null,$limit_start = 0,$limit_count = 1000,$index_by = null){
    $sql="SELECT ";
    switch ($select) {
      case "all":
        $sql .= "timestamp,type,value";
      break;
      case "min_val":
        $sql .= "min(value)" ;
      break;
      case "max_val":
        $sql .= "max(value)" ;
      break;
      case "avg_val":
        $sql .= "avg(value)"   ;
      break;
      default:
    	  throw new Exception(__METHOD__."->select not vaild");
    	break;
      
    }
     $wb = new Where_Builder();
     $sql .=" FROM `weather_data` ";
     switch ($type) {
      
       case "*":
       break;     	 
       default:
       	 $wb->add("`type`=\"".$this->database->getEscaped($this->getTypeID($data["type"]))."\"");
       break;
     }
    if (isset($timestamp_from) ) {
    	$wb->add("`timestamp` >=".$this->database->getEscaped($timestamp_from));
    }
    if (isset($timestamp_to) ) {
    	$wb->add("`timestamp` <".$this->database->getEscaped($timestamp_to));
    }
    
    $sql.= $wb->get() . " LIMIT ".$this->database->getEscaped($limit_start).",".$this->database->getEscaped($limit_count);
    
    
    $this->database->setQuery($sql);
    return $this->database->loadAssocList(null,$index_by);  	
  }
  
  protected function getTypeID($identifier){
    while (!$this->type_ids[$identifier]) {
      if ($this->type_ids === false) {
        $sql="SELECT id,CONCAT(`type_name`,'::',`unit`) as identifier FROM `types`";
        $this->database->setQuery($sql);
        $this->type_ids =  $this->database->loadAssocList("id","identifier"); 
      }
      if (empty($this->type_ids[$identifier])) {
        list($type_name,$unit) = explode("::",$identifier);
      	 $sql = "INSERT INTO `types` (`id`, `type_name`, `unit`) 
                  VALUES (NULL
                          , ".$this->database->getEscaped($type_name)."
                          , ".$this->database->getEscaped($unit)."
                          
                          );";
        //echo $sql;
      	$db->setQuery($sql);
        $db->query();
        $this->type_ids = false;
      }
    }
    return $this->type_ids[$identifier];
  }
  
}