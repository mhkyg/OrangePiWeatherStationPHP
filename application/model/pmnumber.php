<?

class PmNumber{
  protected $number,$precision;
  
  static function _new($value,$precision=null){
    return new self($value,$precision);
  }
  
  /**
  return number of decimals in $value
  if $true_precision==true, then trailing zeros won't be counted
  */
  final static function detectPrecision($value,$true_precision=false){
    if (($pos=strpos($value, "."))!==false){
      if ($true_precision) $value=rtrim($value, '0');
      return strlen($value)-1-$pos;
    } else {
      return 0;
    }
  }
  
  function __construct($value="0",$precision=null){
    $this->set($value);
    if (!isset($precision)){
      $this->precision=3;
      if (($pos=strpos($value, "."))!==false){
        $auto_precision=strlen($value)-1-$pos;
        if ($auto_precision>3) $this->precision=$auto_precision; 
      }
    } else {
      $this->precision=$precision;
    }
    //$this->number=str_replace(",",".", (string)$value);
  }
  
  function getPrecision(){
    return $this->precision;
  }
  
  function set($value){
    $this->number=str_replace(",",".", (string)$value);
    return $this;
  }
  
  function add($number){
    $this->number=bcadd($this->number, (string)$number,$this->precision);
    return $this;
  }
  
  function getAdd($number){
    $ret=clone $this;
    $ret->add($number);
    return $ret;
  }

  function sub($number){
    $this->number=bcsub($this->number, (string)$number,$this->precision);
    return $this;
  }

  function getSub($number){
    $ret=clone $this;
    $ret->sub($number);
    return $ret;
  }
  
  function div($number){
    $this->number=bcdiv($this->number, (string)$number,$this->precision);
    return $this;
  }

  function getDiv($number){
    $ret=clone $this;
    $ret->div($number);
    return $ret;
  }

  function mul($number){
    $this->number=bcmul($this->number, (string)$number,$this->precision);
    return $this;
  }
  
  function getMul($number){
    $ret=clone $this;
    $ret->mul($number);
    return $ret;
  }

  function mod($number){
    $this->number=bcmod($this->number, (string)$number);
    return $this;
  }
  
  function getMod($number){
    $ret=clone $this;
    $ret->mod($number);
    return $ret;
  }
  
  function floor(){
    $result = bcmul($this->number, '1', 0);
    if ((bccomp($this->number, '0', $this->precision) === -1) && bccomp($this->number, $result, $this->precision))
        $result = bcsub($result, 1, 0);

    $this->number=$result;
    return $this;
  }
  
  function getFloor(){
    $ret=clone $this;
    $ret->floor();
    return $ret;
  }
  
  function ceil(){
    $result = bcmul($this->number, '1', 0);
    if ((bccomp($this->number, '0', $this->precision) !== -1) && bccomp($this->number, $result, $this->precision))
        $result = bcadd($result, 1, 0);
    $this->number=$result;
    return $this;
  }
  
  function getCeil(){
    $ret=clone $this;
    $ret->ceil();
    return $ret;
  }
  
  function round($precision=0){
    $precision++;
    
    $half_fraction=bcmul("5",bcpow("10",-$precision,$precision),$precision);
    if (bccomp($this->number, '0', $this->precision) === -1){
      $half_fraction=bcmul($half_fraction,"-1",$precision);
    }
    $this->number=bcadd($this->number,$half_fraction,$precision);
    $this->number=bcmul($this->number,"1",$precision-1);
    
    return $this;
  }
  
  function getRound($precision=0){
    $ret=clone $this;
    $ret->round($precision);
    return $ret;
  }
  
  function trunc($precision=0){
    $this->number=bcmul($this->number, '1', $precision);
    return $this;
  }

  function getTrunc($precision=0){
    $ret=clone $this;
    $ret->trunc($precision);
    return $ret;
  }

  function comp($number){
    return bccomp($this->number,(string)$number,$this->precision);
  }
  
  function isEq($number){
    return bccomp($this->number,(string)$number,$this->precision)===0;
  }
  
  function isGt($number){
    return $this->comp($number)===1;
  }
  
  function isGtEq($number){
    return $this->comp($number)!==-1;
  }
  
  function isLt($number){
    return $this->comp($number)===-1;
  }

  function isLtEq($number){
    return $this->comp($number)!==1;
  }
  
  function abs(){
    if (bccomp($this->number,0,$this->precision)===-1) $this->mul(-1);
    return $this;
  }
  
  function getAbs(){
    $ret=clone $this;
    $ret->abs();
    return $ret;
  }
  
  function __toString(){
    return $this->get();
  }
  
  function trimZeros(){
    $this->number=$this->get(true);
    return $this;
  }
  
  function get($trim_rezos=true){
    if ($trim_rezos)
      if (strpos($this->number, ".")!==false) return rtrim(rtrim($this->number,"0"),".");
    return (string)$this->number;
  }
}