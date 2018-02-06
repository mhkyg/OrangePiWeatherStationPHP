<?

abstract class TheDatabase {
  private $errors=array();
  
  /**
  Set a query string to be executed.
  must return self, aka $this
  */
  function setQuery($sql){} 

  function getQuery(){} 

  /**
  Execute a previously set query.
  Does not return results, use loadAssocList() for that purpose.
  */
  function query(){} 
  
  /**
  If you only want to get a one-dimentional array of a single field,then specify that field name in the $fieldname parameter.
  $index_by_field indexes the returned rows by the value of the given field, for each row.    
  */
	function loadAssocList($fieldname="",$index_by_field=""){}
  
  function insertid(){}
  
  function getEscaped($text){}
  
  function getAffectedRows(){}
  
  /**
	return array(field_name=>array("Field"=>(string),"Type"=>, "Null"=>{YES|NO} , "Default"=> ))
  */
  function getTableFields($tablename){}

  function error($error_code,$error_msg){
    $this->errors[$error_code]=$error_msg;
  }
  /**
  return array(error_code=>error_msg)
  */
  function getErrors(){
    $err=$this->errors;
    $this->errors=array();
    return $err;
  }
}

class TheDatabase_MySQLi extends TheDatabase {
  protected $_resource,$encoding="utf8";
  private $_query;
  
  /**
  throws exception if fails
  */
  function connect($username,$password,$location,$database,$port=null){
    if (!($this->_resource=mysqli_connect($location, $username, $password,$database,$port))){
      throw new Exception("Could not connect to database!");
    }
    $this->setEncoding();
  }
  
	/**
  throws exception if fails
  */
  function setEncoding(){
    if (!mysqli_set_charset($this->_resource,$this->encoding)){
      throw new Exception("Database set charset failed!");
    }
	}
	
	function getEncoding(){
		return $this->encoding;
	}
  
  /**
  Set a query string to be executed.
  */
  function setQuery($sql){ 
  	$this->_query=(string)$sql;
  	return $this;
  } 

  final function getQuery(){ 
  	return $this->_query;
  } 


  /**
  Execute a previously set query.
  Does not return results, use loadAssocList() for that purpose.
  */
  function query(){ 
    if (!empty($this->_query)) {
      if ($res=mysqli_query($this->_resource,$this->_query)) {
      	return $res;
      }else{
        $this->error(mysqli_errno($this->_resource),mysqli_error($this->_resource));
        return false;
      } 	
    }else{
      $this->error("NO_QUERY_SPECIFIED");
      return false;
    }
  	
  } 
  
  function getAffectedRows(){
    return mysqli_affected_rows($this->_resource);
  }
  
  /**
  If you only want to get a one-dimensional array of a single field,then specify that field name in the $fieldname parameter.
  Specify $index_by_field to index the returned rows by the value of the given field, for each row.    
  */
	function loadAssocList($fieldname="",$index_by_field=""){
		if (!($cur = $this->query())) return null;
		
		$array=array();
		
		if (empty($fieldname)){
		  if (empty($index_by_field)){
    		while (($row = mysqli_fetch_assoc($cur))!==null ) $array[]=$row;
      } else {
    		while (($row = mysqli_fetch_assoc($cur))!==null ) $array[$row[$index_by_field]]=$row;
      }
    } else{
		  if (empty($index_by_field)){
    		while (($row = mysqli_fetch_assoc($cur))!==null ) $array[]=$row[$fieldname];
    	} else {
    		while (($row = mysqli_fetch_assoc($cur))!==null ) $array[$row[$index_by_field]]=$row[$fieldname];
      }	
    }  
		
		mysqli_free_result($cur);
		return $array;
	}
  
  function insertid(){
    return mysqli_insert_id( $this->_resource );
  }
  
  function getEscaped($text){
		return mysqli_real_escape_string( $this->_resource, $text ); 
	}
  
	function getTableFields($table){
		$result = array();

		$this->setQuery('SHOW FIELDS FROM `'.$table.'`');
		
		if (!($cur = $this->query())) return null;
		
		while (($row = mysqli_fetch_assoc($cur))!==null) {
			$result[$row['Field']] = $row;
		}
		mysqli_free_result( $cur );

		return $result;
	}
	/*
	function __destruct(){
	  //mysqli_close($this->_resource);
  }
  */
}