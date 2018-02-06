<?
class Where_Builder{
  protected $where_string="";

  function add($where_string,$operator="AND" ){
    if (trim($where_string)==="") return;  
    if (!empty($this->where_string)) $this->where_string.=" ".$operator." ";
    $this->where_string.=$where_string;
  }
  
  function get($nowhere=false){
    return ((empty($this->where_string) or $nowhere) ? "" : " WHERE ").$this->where_string;
  }
}