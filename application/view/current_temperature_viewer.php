<?


class Current_Weather_Viewer extends Data_Viewer_Abstract_Page{
  
  function __construct($working_dir){
    $this->dir = $working_dir;
  }
  
  protected function getData(){
    $fp = fopen($filename, 'r+');
    $count = 0;
    while(!(flock($fp, LOCK_NB))) {
       usleep(100);
       if ($count++ > 10) break; 
       $lock = true;
    }
    if ($lock) {
      $data = unserialize(file_get_contents($this->dir."/../data.txt")) ;	
    }
    return $data  ;	
  }

  protected function getTemperatureCategory($temp){

  	if ($temp>=21 and $temp<30) {
   	  $res = 3;
    }
  	if ($temp>=10 and $temp<21) {
   	  $res =  2;
    }
  	if ($temp<10) {
   	  $res = 1;
    }
  	if ($temp>=30) {
   	  $res = 4;
    }

    return $res;
  }  
  function show(){
    $data = $this->getData();
    
    $para_util = new Absolute_Humidity_Util();
    $szelloztetessel_elerheto = $para_util->getRelativeHumidityOnDifferentTemperature($data["homerseklet"][1][1],$data["homerseklet"][1][2],$data["homerseklet"][0][1]);
    $para_felso_hatar = 60;
    $kivanatos_para_tartalom = 45;
    $para_tolerancia = 5;
    if ( (abs($szelloztetessel_elerheto-$kivanatos_para_tartalom) < abs($data["homerseklet"][0][2]-$kivanatos_para_tartalom)) 
          or ( ($data["homerseklet"][0][2] - $kivanatos_para_tartalom - $para_tolerancia > 0) and $kivanatos_para_tartalom > $szelloztetessel_elerheto )
          or ($data["homerseklet"][0][2] - $para_felso_hatar > 0 )  ){
      //Szellőztetés javasolt
      $para_javaslat_class="alert alert-danger";
      $para_javaslat="Szellőztetés javasolt";
    }else{
      //Szellőztetés nem javasolt
      $para_javaslat_class="alert alert-success";
      $para_javaslat="Szellőztetés nem javasolt";
    }
  	?>

       <div class="row">
    
        <div class="col-md-6">
          <? $this->showHomersekletBox("Bent","glyphicon glyphicon-home",$data,0);?>               
        </div>
        <div class="col-md-6">
          <? $this->showHomersekletBox("Kint","glyphicon glyphicon-log-out",$data,1);?>      
        </div> 
       
         <div class="col-md-6"> 
           <div class="panel panel-default">
            <div class="panel-heading">Szellőztetéssel elérhető páratartalom</small></div>
                <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                      <span class="humidity"><?=$szelloztetessel_elerheto?>%</span>                  
                    </div>
                    <div class="col-sm-6">
                               <div class="<?=$para_javaslat_class;?>">
                       <?=$para_javaslat?>
                    </div> 
                    </div>
                  </div>    
                </div>
           </div>
         </div>
       </div>   
       <div class="debug_data">
        <?
          $load = new CPU_Info();
      
          echo '<hr>';
          if ($res = $load->getServerLoad()) {
            echo " load:".	$res["load"]. " cpu_count:".$res["cpus"]." percent: ".($res["load"]/$res["cpus"] *100)."%";
          }         
        ?>
       </div>
    <?
  } 
  function showHomersekletBox($heading,$icon_class,$data,$index){

    if (!isset($data["homerseklet"][$index][1]) ) {
    	?>
      <div class="alert alert-danger">
        <strong>DHT22</strong> Utolsó hibátlan adat:<?=date("Y-m-d H:i",$data["last_valid_data_timestamp"]);?>
      </div>      
      <?
      return;
    }
  	?>
        <div class="panel panel-default">
            <div class="panel-heading"><?=$heading;?> <small><?=date("Y-m-d H:i",$data["ido"]);?></small></div>
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-2 hidden-sm-down">
                 <span class="<?=$icon_class?>"></span>
                </div>
                <div class="col-sm-5">
                  <span class="homerseklet homerseklet<?=$this->getTemperatureCategory($data["homerseklet"][$index][1])?>"><?=$data["homerseklet"][$index][1]?>&#x2103;</span>
                  
                </div>
                <div class="col-sm-5">
                  <span class="humidity"><?=$data["homerseklet"][$index][2]?>%</span>
                </div>
              </div>
             
            </div>
          </div>    
    <?
  }
   
}





