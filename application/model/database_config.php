<?
class Database_Config implements ArrayAccess {
  private $user = "";
  private $password = "";
  private $host = "" ;
  private $database = ""  ;
  
  public function offsetSet($offset, $value) {
   return false;
        
  }

  public function offsetExists($offset) {
   
    return isset($this->$offset);
     
  }

  public function offsetUnset($offset) {
    //unset($this->$offset);
  }

  public function offsetGet($offset) {
      return isset($this->$offset) ? $this->$offset : null;
  }
  

}