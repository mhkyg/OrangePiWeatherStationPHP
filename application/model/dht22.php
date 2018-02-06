<?

class PHP_DHT22{
  private $gpio;
  
  
  const STATE_INIT_PULL_DOWN = 1;
  const STATE_INIT_PULL_UP = 2;
  const STATE_DATA_FIRST_PULL_DOWN = 3;
  const STATE_DATA_PULL_UP = 4;
  const STATE_DATA_PULL_DOWN = 5;
  
	function __construct(PHP_GPIO $gpio,$port_number){

		$this->gpio = $gpio;
    $this->port_number = $port_number;
	}
  
  /**
   *return array("temperature"=>float,"humidity"=>float);
   */
                                         
  function getTemperatureAndHumidity(){
    //Busy wait to wake cpu from sleep
    $ts = microtime(true);
    $i=0;
    while ($ts+1>microtime(true) ) {
    	$i++   ;
    }
    
    $this->gpio->sendBits($this->port_number,array(array("value"=>0,"delay_after_microsec"=>1000)
                                                   ,array("value"=>1,"delay_after_microsec"=>20) 
                                                   //,array("value"=>0,"delay_after_microsec"=>0)
                                                       
                                                        ));
                                                      
   
    
    $res = $this->parseData($this->gpio->readContinous($this->port_number));
    
      	
    $generated_checksum = ($res[0]+$res[1]+$res[2]+$res[3]) % 256;
    $temperature_sign = 1;
    $templerature_first_byte = $res[2];
    if ($templerature_first_byte>127) {
    	$temperature_sign = -1; 
      $templerature_first_byte-=128;     
    }
    $humidity = ($res[0]*256+$res[1])/(10);  
    $temperature = $temperature_sign*(($templerature_first_byte )*256+$res[3])/10;
    if ($generated_checksum - $res[4] > 1 or $humidity>100 or $humidity<0 or $temperature > 80 or $temperature < -40) {
    	throw new Exception("Checksum error (generated:".$generated_checksum." received:".$res[4]." raw:".print_r($res,true).")");
    }


    return array("temperature"=>$temperature,"humidity"=>$humidity);
  }
  /**
   *$raw_data = array of bool
   */
  
  private function parseData($raw_data){
    /**
    
     */
     $length = array();
     $last_value = 0;
     $index = 0;
     $state = self::STATE_DATA_FIRST_PULL_DOWN   ;
     $current_length = 0;
     foreach ($raw_data["results"] as $value) {
        $current_length ++;
        /*
        if ($state == self::STATE_INIT_PULL_DOWN and $value == 0) {
          $state = self::STATE_INIT_PULL_UP;
        }
        if ($state == self::STATE_INIT_PULL_UP and $value == 1) {
          $state = self::STATE_DATA_FIRST_PULL_DOWN;
        }*/ 
        if ($state == self::STATE_DATA_FIRST_PULL_DOWN and $value == 0) {
          $state = self::STATE_DATA_PULL_UP;
        }
        if ($state == self::STATE_DATA_PULL_UP and $value == 1) {
          $current_length = 0;
          $state = self::STATE_DATA_PULL_DOWN;           
        } 
        if ($state == self::STATE_DATA_PULL_DOWN and $value == 0) {
        	$length[]= $current_length;
          $state = self::STATE_DATA_PULL_UP;
        }                       
     }
     
    $lowest = min($length);
    $highest = max($length);
    $middle = ($highest+ $lowest)/2; 
    
    $bit_array = array(0);
    foreach ($length as $key=>$value) {
      $bit_array[] = ($value>=$middle)?(1):(0);	
    }
     
 
    
    $result = $this->convertBitsToBytes($bit_array);
    

    return $result;   	
  }
  
  private function convertBitsToBytes($bit_array){
    $bit_string = implode("", $bit_array);
    for ($i=0;$i<(count($bit_array)/8 ); $i++) {
      $byte_as_bin = substr($bit_string, $i*8, 8);

    
      $result[] = bindec($byte_as_bin) ;	
    }
    
    return $result;
  }
  
}