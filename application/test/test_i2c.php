<?
require_once "../model/auto_loader.php";

  $gpio =  new PHP_GPIO("/sys/class/gpio");
  $i = 118;
  echo "\nstart_read";
  $res = $gpio->readI2C(11,12,$i,32);
  echo "\nend_read";
  //debug
  echo "\n";
  var_dump($i,$res);
 	
$gpio->i2cReset(11,12);

