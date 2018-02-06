<?

class Translation{
  private $lang_code;
	function __construct($lang_code = "en"){
		$this->lang_code = $lang_code; 
	}
  /**
   return field value;
   */
  function get($field_key){
    if (isset($result = $translations[$this->lang_code][$field_key]) ) {
    	return $result;
    }else{
      return "_untranslated:".$field_key; 
    }	
  }
  
  private $translations = array(
    "en"=>array(
      "outside:lcd_screen"=>"out"
     ,"inside:lcd_screen"=>"in" 
    )
    "hu"=>array(
      "outside:lcd_screen"=>"ki"
     ,"inside:lcd_screen"=>"be" 
    )    
  );
  
}