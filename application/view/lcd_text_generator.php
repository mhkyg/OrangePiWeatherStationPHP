<?
/**
 *TODO:Update
 */
class LCD_Text_Generator{
  private $dhtresult;
	function __construct($dhtresult){
		$this->dhtresult = $dhtresult;
	}
  
  function generate($kijelzo_file_name){
    $data =  explode("\n",$this->dhtresult);
    $exp = function($item){
      $parts = explode(";",$item);
      
      return $parts;
    };  
    $data = array_map($exp, $data);
   
    $lcd_row_length = 16;  
    $translation = new Translation();
    $first_part_str = $translation->get("outside:lcd_screen").str_pad($data[1][1],5," ",STR_PAD_LEFT)."C";
    $second_part_str = $data[1][2]."%";
    
    $lcd_string = $first_part_str.str_pad($second_part_str,$lcd_row_length-strlen($first_part_str)," " ,STR_PAD_LEFT );
    
    $first_part_str = $translation->get("inside:lcd_screen").str_pad($data[0][1],5," ",STR_PAD_LEFT)."C";
    $second_part_str = $data[0][2]."%";
    
    
    $lcd_string.= "\n".$first_part_str.str_pad($second_part_str,$lcd_row_length-strlen($first_part_str)," " ,STR_PAD_LEFT );
    //$kijelzo_string.= "\n".date("m-d H:i");
    //file_put_contents($kijelzo_file_name, $kijelzo_string );   
  //$lcd = `/usr/bin/python3 /var/www/lcd_update.py`;
    return $lcd_string;  
  }
  
}
