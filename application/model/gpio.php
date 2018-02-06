<?

class PHP_GPIO{
  private $basepath,$port_direction_cache =array(),$export_cache=array(),$file_handler_cache =array();
  
  /**
   * $basepath
   */                              
	function __construct($basepath){
		$this->basepath = rtrim($basepath, "/") ;

	}
  /**
   * $port_number: 0-16
   *
   */
   
  
  private function initGPIO($port_number){
  	//only exportit if it's already not exported

    if (empty($this->file_handler_cache[$port_number]) ) {
      file_put_contents($this->basepath."/export", $port_number)  ;
    	$this->file_handler_cache[$port_number] = fopen($this->basepath."/gpio".$port_number."/value", "r+");
    }
    
  }
  
  /**
   * $value = bool
   * $port_number: 0-16
   */
  function setGPIOvalue($port_number,$value) {    
    $this->initGPIO($port_number);
    if ($this->port_direction_cache[$port_number]!=="out") {
      file_put_contents($this->basepath."/gpio".$port_number."/direction", "out");
      //echo "\nsetting_out:".$port_number;
      $this->port_direction_cache[$port_number]="out";	
    }
    fseek($this->file_handler_cache[$port_number], 0);
    fwrite($this->file_handler_cache[$port_number],(int)(bool)$value, 1);
    //file_put_contents($this->basepath."/gpio".$port_number."/value", (int)((bool)$value ) );
    
    //echo "\nout: ".$port_number."->".$value;
        
  }
  /**
   * $value = bool
   * $port_number: 0-16
   */  
  function getGPIOvalue($port_number){
    $this->initGPIO($port_number);
  	
    if ($this->port_direction_cache[$port_number]!=="in") {
      file_put_contents($this->basepath."/gpio".$port_number."/direction", "in");
      $this->port_direction_cache[$port_number]="in";
      //echo "\nseting_in:".$port_number;
    } 
    fseek($this->file_handler_cache[$port_number], 0);
    return (bool)fread($this->file_handler_cache[$port_number],1);  
    //return (bool)(file_get_contents($this->basepath."/gpio".$port_number."/value"));
  }
  /**
   * delay = delay between reads in microsec
   * $port_number: 0-16
   * return (int) 0-255 
   */    
  function getGPIOBits($port_number,$delay){
    $res = 0;
    //$pow_2 = array(0,1,2,4,8,16,32,64,128);
    for ($i=0;$i<8 ;$i++ ) {
    	$res = $pow_2[$i] * $this->getGPIOvalue($port_number);
      usleep($delay);
    }
    return $res;  	
  }
  
  /**
   * $port_number: 0-16
   * return array("results"=>array of bool or null,"total_time_sec"=>float);
   * 
   **/
   
  
  function readContinous($port_number,$max_unchanged = 100){
  	$last_gpio_state = 0;
    $data = array();
    $index = 0;
    $ts = microtime(true);
    
    
    $raw_data = array();
    $read = 0;
    while ($unchanged < $max_unchanged) {
      $raw_data[$read] = $current = $this->getGPIOvalue($port_number);      
      $read++;
      if ($last_gpio_state == $current) {
        $unchanged++;
      }else{
        $unchanged = 0;
        $last_gpio_state =  $current;
        
      }
    }
    $total_time = (microtime(true)-$ts);
    //$raw_data = array_slice($max_unchanged, 0, -$max_unchanged,true);
    
    return  array("results"=>$raw_data,"total_time_sec"=>$total_time);
    
  }
  
  
  /**
   * $values_with_timeings = array(array("value"=>1 or 0,"delay_after_microsec"=>microseconds:int) );
   */
  function sendBits($port,$values_with_timeings){
    
    foreach ($values_with_timeings as $data) {
      
    	$this->setGPIOvalue($port,$data["value"]);
      usleep($data["delay_after_microsec"]);
    }
    
  }  
  private $last_i2c_clk_change;
  private $i2c_clock_interval_sec = 1/1000;
  /**
   * uses i2c @ 1000Hz
   */
   /*
  function readI2C($clk_port, $data_port, $address,$register,$number_of_bits_to_read){
  	// To begin set ports to 1
    $this->setGPIOvalue($clk_port,1);
    $this->setGPIOvalue($data_port,1);
  	// To pull data low then clocl_low with delay so device will understand
    $this->setGPIOvalue($data_port,0);
    usleep(1000);
    $this->setGPIOvalue($clk_port,0);
    $this->last_i2c_clk_change = microtime(true);
    
     
    $address_as_bits = $this->byte_to_bit_string($address) ;

   
    for ($i=0; $i<strlen($address_as_bits) ; $i++) {
      $this->sendI2Cbit($clk_port, $data_port,$address_as_bits[$i]);
      
    }
    //End address
    $register
    //Send 1 so that request is a write request
    $this->sendI2Cbit($clk_port, $data_port,1);
    $register_as_string = $this->byte_to_bit_string();
    //send register
    for ($i=0; $i<strlen($register_as_string) ; $i++) {
      $this->sendI2Cbit($clk_port, $data_port,$register_as_string[$i]);
    }

    
  	// Send repeated start
    $this->setGPIOvalue($data_port,0);
    usleep(1000);
    $this->setGPIOvalue($clk_port,0);    
    
    //Send 0 so that request is a read request
    //$this->sendI2Cbit($clk_port, $data_port,0);
    $ack = $this->readI2Cbit($clk_port, $data_port);
    if ($ack) {
    	Throw new Exception("I2C ack not received");
    }
    $result = "";
    for ($i=0; $i<$number_of_bits_to_read; $i++) {
      $value = $this->readI2Cbit($clk_port, $data_port);
    	if ($value) $result.="1"; else $result.="0";
    }    
    $next = $this->last_i2c_clk_change + $this->i2c_clock_interval_sec;
    while ( microtime(true)<$next ) {
     	usleep(50);
    }    
    //Send stop 
    $this->setGPIOvalue($clk_port,1);
    usleep(1000);
    $this->setGPIOvalue($data_port,1);
    return $result;
  }
  
  protected function readI2Cbit($clk_port, $data_port){
      $next = $this->last_i2c_clk_change + $this->i2c_clock_interval_sec;
     
      
      //wait to pull up clock
      while ( microtime(true)<$next ) {
      	usleep(50);
      }
      //pull up clock
      $this->setGPIOvalue($clk_port,1);
      $this->last_i2c_clk_change = microtime(true);
      $next = $this->last_i2c_clk_change + $this->i2c_clock_interval_sec;
      // get data
      $bit = $this->getGPIOvalue($data_port);
      //wait to pull down clock
      while ( microtime(true)<$next ) {
      	usleep(50);
      }    
      $this->last_i2c_clk_change = microtime(true);  
      //pull down clock
      $this->setGPIOvalue($clk_port,0);
      return $bit;      	  
  }
  
  protected function sendI2Cbit($clk_port, $data_port,$bit){
      $next = $this->last_i2c_clk_change + $this->i2c_clock_interval_sec;
      //set data
      $this->setGPIOvalue($data_port,$bit);
      //wait to pull up clock
      while ( microtime(true)<$next ) {
      	usleep(50);
      }
      //pull up clock
      $this->setGPIOvalue($clk_port,1);
      $this->last_i2c_clk_change = microtime(true);
      $next = $this->last_i2c_clk_change + $this->i2c_clock_interval_sec;
      //wait to pull down clock
      while ( microtime(true)<$next ) {
      	usleep(50);
      }    
      $this->last_i2c_clk_change = microtime(true);  
      //pull down clock
      $this->setGPIOvalue($clk_port,0);      	
  }

  function i2cReset($clk_port, $data_port){
  	 for ($i=0;$i<100 ;$i++ ) {
    	 $this->setGPIOvalue($clk_port,1);
       usleep(1000);
       $this->setGPIOvalue($clk_port,0);
    }
  }
  */   
  protected function byte_to_bit_string($byte){
  	$byte = min(max(0,$byte),255);
    return str_pad(decbin($byte), 8, 0, STR_PAD_LEFT);
    
  }
  
}
