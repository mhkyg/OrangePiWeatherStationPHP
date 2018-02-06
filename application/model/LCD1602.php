<?

class LCD_2x16_Manager {

 
 
  const LCD_WIDTH = 16;
  const LCD_CHR = True;
  const LCD_CMD = False;
  const LCD_LINE_1 = 0x80;
  const LCD_LINE_2 = 0xC0;
  const E_PULSE = 500;
  const E_DELAY = 500; 
  const LCD_SETCGRAMADDR = 0x40;
  
  private $port_mapping = array();
  /**
     array("LCD_POWER"=>
           ,"LCD_RS"=>
           ,"LCD_D4"=>
           ,"LCD_D5"=>
           ,"LCD_D6"=>
           ,"LCD_D7"=>
           ,"LCD_E"=>
     );
   */ 
  final function __construct($gpio,$port_mapping) {
    $this->gpio_manager=      $gpio;
    $this->port_mapping=      $port_mapping;
  }
 
  /**
   * Turn LCD backlight off
   */
  function turnOffLCD() {
    $this->gpio_manager->setOutput($this->port_mapping['LCD_POWER'],false);
    $this->lcd_byte(0x01,self::LCD_CMD);
  }
   
  /**
   * Turn LCD backlight on
   */
  function turnOnLCD() {
    $this->gpio_manager->setOutput($this->port_mapping['LCD_POWER'],true);
  }
 
  /**
   * Clear LCD display
   */
  function clearLCD() {
    $this->lcd_byte(0x01, self::LCD_CMD);
  } 
 
  /**
   * Write text to LCD display line 1 or 2
   */
  function writeLCD($message, $line) {
    if (!in_array($line, array(1,2), true)) return;
    $message = $this->handleSpecialCharacters($message);
    $message = mb_substr($message, 0, self::LCD_WIDTH);
    $message = str_pad($message, self::LCD_WIDTH, " ", STR_PAD_RIGHT);
    
    if ($line === 1) {
      $this->lcd_byte(self::LCD_LINE_1, self::LCD_CMD);
    }
    if ($line === 2) {
      $this->lcd_byte(self::LCD_LINE_2, self::LCD_CMD);
    }
    foreach (str_split($message) as $char) {
      $this->lcd_byte(ord($char),self::LCD_CHR);
    }
  }
 
  function initLCD() {
    $this->lcd_byte(0x33,self::LCD_CMD); // 110011 Initialise
    $this->lcd_byte(0x32,self::LCD_CMD); // 110010 Initialise
    $this->lcd_byte(0x06,self::LCD_CMD); // 000110 Cursor move direction
    $this->lcd_byte(0x0C,self::LCD_CMD); // 001100 Display On,Cursor Off, Blink Off
    $this->lcd_byte(0x28,self::LCD_CMD); // 101000 Data length, number of lines, font size
    $this->lcd_byte(0x01,self::LCD_CMD); // 000001 Clear display
    usleep(self::E_DELAY);
  }
 
  /**
   * Send byte to data pins
   * bits = data
   * mode = True  for character
   *        False for command
   */
  protected function lcd_byte($bits, $mode) {

    $this->gpio_manager->setOutput($this->port_mapping['LCD_RS'], $mode);
 
    // High bits
    $high_bits['LCD_D4'] = false;
    $high_bits['LCD_D5'] = false;
    $high_bits['LCD_D6'] = false;
    $high_bits['LCD_D7'] = false;
    if (($bits & 0x10)==0x10) {
      $high_bits['LCD_D4'] = true;
    }
    if (($bits & 0x20)==0x20) {
      $high_bits['LCD_D5'] = true;
    }
    if (($bits & 0x40)==0x40) {
      $high_bits['LCD_D6'] = true;
    }
    if (($bits & 0x80)==0x80) {
      $high_bits['LCD_D7'] = true;
    }
    foreach ($high_bits as $key=>$value) {
      $this->gpio_manager->setGPIOvalue($this->port_mapping[$key], $value);
    }
 
    // Toggle 'Enable' pin
    $this->lcd_toggle_enable();
 
    // Low bits
    $low_bits['LCD_D4'] = false;
    $low_bits['LCD_D5'] = false;
    $low_bits['LCD_D6'] = false;
    $low_bits['LCD_D7'] = false;
    if (($bits & 0x01)==0x01) {
      $low_bits['LCD_D4'] = true;
    }
    if (($bits & 0x02)==0x02) {
      $low_bits['LCD_D5'] = true;
    }
    if (($bits & 0x04)==0x04) {
      $low_bits['LCD_D6'] = true;
    }
    if (($bits & 0x08)==0x08) {
      $low_bits['LCD_D7'] = true;
    }
    foreach ($low_bits as $key=>$value) {
      $this->gpio_manager->setGPIOvalue($this->port_mapping[$key], $value);
    }
 
    // Toggle 'Enable' pin
    $this->lcd_toggle_enable();
  }
   

  protected function lcd_toggle_enable() {
    usleep(self::E_DELAY);
    $this->gpio_manager->setGPIOvalue('LCD_E', true);
    usleep(self::E_PULSE);
    $this->gpio_manager->setGPIOvalue('LCD_E', false);
    usleep(self::E_DELAY);
  }
  
  //https://www.quinapalus.com/hd44780udg.html
  //http://www.circuitvalley.com/2012/02/lcd-custom-character-hd44780-16x2.html
  protected function create_char($code, $pattern){
    if (!($code < 8 and $code >= 0) ) {
      Throw new Exception("code must be 0<= ".$code."<8");    	
    }
    for ($i = 0;$i<8 ;$i++ ) {
    	$this->lcd_byte($pattern[$i],true);
    }
  } 
  
  private $special_characters_map = array(
     "á"=>array(2,4,14,1,15,17,15,0)
    ,"é"=>array(2,4,14,17,31,16,14,0)
    ,"í"=>array(2,4,12,4,4,4,14,0)
    ,"ű"=>array(5,10,0,17,17,19,13,0)
    ,"ú"=>array(2,4,17,17,17,19,13,0)
    ,"ő"=>array(5,10,14,17,17,17,14,0)
    ,"ö"=>array(10,0,14,17,17,17,14,0)
    ,"ü"=>array(10,0,0,17,17,17,14,0)
    ,"ó"=>array(2,4,14,17,17,17,14,0)
    ,"Á"=>array(2,4,14,17,31,17,17,0)
    ,"É"=>array(2,4,14,8,12,8,14,0)
    ,"Í"=>array(2,4,14,4,4,4,14,0)
    ,"Ű"=>array(5,10,0,17,17,17,14,0)
    ,"Ú"=>array(2,21,17,17,17,17,14,0)
    ,"Ő"=>array(5,10,14,17,17,17,17,14)
    ,"Ö"=>array(10,0,14,17,17,17,17,14)
    ,"Ü"=>array(10,0,17,17,17,17,14,0)
    ,"Ó"=>array(2,4,14,17,17,17,17,14)
  );
  /**
   Replace characters 
   Since only 8 custom characters can be used at once it will replace all characters after 8th to "_"
   return replaced string
   */
  protected function handleSpecialCharacters($message){
    $special_char_replace=array() ;
    $count = 0;
  	for ($i=0;$i<mb_strlen($message) ; $i++) {
      $char = mb_strcut($message,$i,1,"UTF-8");
   	  if (isset($this->special_characters_map[$char] )){
        if ($count<8) {
          //We can create a new custom character
          $current_pos = $count++;
        	$this->create_char($current_pos, $this->special_characters_map[$char]);
          $special_char_replace[$char] = $current_pos;
        }else{
            // We ran out of custom characters for this string.
           $special_char_replace[$char] = "_";
        }
      } else{
        //NON ASCII characters that were not found in "special_characters_map"
        if (ord($char) > 127 or ord($char) < 32 ) {
          $special_char_replace[$char] = "_";	
        }
      }
    }
    //This is a binary safe function so no probles should occur.
    return strtr($message, array($special_char_replace) ) ;
    
  }      
 

 
}