<?


class Weather_Statistics_Viewer extends Data_Viewer_Abstract_Page{
  
  function __construct($database){
    $this->database = $database;
	}
  
  protected function getSQL($agregate_function, $column, $start_time){
  	$sql="SELECT * FROM `homerseklet_adatok` WHERE `".$column."` = (SELECT ".$agregate_function."(`".$column."`) FROM `homerseklet_adatok` WHERE timestamp > ".$start_time." ) AND timestamp > ".$start_time."";
    return $sql;
  }
  
  protected function getData(){
    $db=$this->database;
    
    
    
    $sql = $this->getSQL("max","benti_hom",strtotime("-1 day"));    
    $db->setQuery($sql);
    $benti_max_1d=current($db->loadAssocList());
    
    $sql = $this->getSQL("max","kinti_hom",strtotime("-1 day"));    
    $db->setQuery($sql);
    $kinti_max_1d=current($db->loadAssocList());

    $sql = $this->getSQL("max","benti_hom",strtotime("-7 days"));
    $db->setQuery($sql);
    $benti_max_7d=current($db->loadAssocList());
    
    $sql = $this->getSQL("max","kinti_hom",strtotime("-7 days"));
    $db->setQuery($sql);
    $kinti_max_7d=current($db->loadAssocList());

    $sql = $this->getSQL("max","kinti_hom",strtotime("-30 days"));
    $db->setQuery($sql);
    $kinti_max_30d=current($db->loadAssocList());
    
    $sql = $this->getSQL("max","benti_hom",strtotime("-30 days"));
    $db->setQuery($sql);
    $benti_max_30d=current($db->loadAssocList());    

    $sql = $this->getSQL("max","kinti_hom",strtotime("-365 days"));
    $db->setQuery($sql);
    $kinti_max_365d=current($db->loadAssocList());
    
    $sql = $this->getSQL("max","benti_hom",strtotime("-365 days"));
    $db->setQuery($sql);
    $benti_max_365d=current($db->loadAssocList()); 

    $sql = $this->getSQL("min","benti_hom",strtotime("-1 day"));
    $db->setQuery($sql);
    $benti_min_1d=current($db->loadAssocList());
    
    $sql = $this->getSQL("min","kinti_hom",strtotime("-1 day"));    
    $db->setQuery($sql);
    $kinti_min_1d=current($db->loadAssocList());

    $sql = $this->getSQL("min","benti_hom",strtotime("-7 days"));
    $db->setQuery($sql);
    $benti_min_7d=current($db->loadAssocList());
    
    $sql = $this->getSQL("min","kinti_hom",strtotime("-7 days"));
    $db->setQuery($sql);
    $kinti_min_7d=current($db->loadAssocList());
    
    $sql = $this->getSQL("min","kinti_hom",strtotime("-30 days"));
    $db->setQuery($sql);
    $kinti_min_30d=current($db->loadAssocList());   
    
    $sql = $this->getSQL("min","benti_hom",strtotime("-30 days"));
    $db->setQuery($sql);
    $benti_min_30d=current($db->loadAssocList());      

    $sql = $this->getSQL("min","kinti_hom",strtotime("-365 days"));
    $db->setQuery($sql);
    $kinti_min_365d=current($db->loadAssocList());   
    
    $sql = $this->getSQL("min","benti_hom",strtotime("-365 days"));
    $db->setQuery($sql);
    $benti_min_365d=current($db->loadAssocList());   
    
    return array("kinti_max_1d"=>array("ido"=>$kinti_max_1d["timestamp"],1=>$kinti_max_1d["kinti_hom"],2=>$kinti_max_1d["kinti_para"]) 
                ,"benti_max_1d"=>array("ido"=>$benti_max_1d["timestamp"],1=>$benti_max_1d["benti_hom"],2=>$benti_max_1d["benti_para"])
                ,"benti_max_7d"=>array("ido"=>$benti_max_7d["timestamp"],1=>$benti_max_7d["benti_hom"],2=>$benti_max_7d["benti_para"])
                ,"kinti_max_7d"=>array("ido"=>$kinti_max_7d["timestamp"],1=>$kinti_max_7d["kinti_hom"],2=>$kinti_max_7d["kinti_para"])
                ,"benti_max_30d"=>array("ido"=>$benti_max_30d["timestamp"],1=>$benti_max_30d["benti_hom"],2=>$benti_max_30d["benti_para"])
                ,"kinti_max_30d"=>array("ido"=>$kinti_max_30d["timestamp"],1=>$kinti_max_30d["kinti_hom"],2=>$kinti_max_30d["kinti_para"])
                ,"benti_max_365d"=>array("ido"=>$benti_max_365d["timestamp"],1=>$benti_max_365d["benti_hom"],2=>$benti_max_365d["benti_para"])
                ,"kinti_max_365d"=>array("ido"=>$kinti_max_365d["timestamp"],1=>$kinti_max_365d["kinti_hom"],2=>$kinti_max_365d["kinti_para"])

                
                ,"kinti_min_1d"=>array("ido"=>$kinti_min_1d["timestamp"],1=>$kinti_min_1d["kinti_hom"],2=>$kinti_min_1d["kinti_para"]) 
                ,"benti_min_1d"=>array("ido"=>$benti_min_1d["timestamp"],1=>$benti_min_1d["benti_hom"],2=>$benti_min_1d["benti_para"])
                ,"benti_min_7d"=>array("ido"=>$benti_min_7d["timestamp"],1=>$benti_min_7d["benti_hom"],2=>$benti_min_7d["benti_para"])
                ,"kinti_min_7d"=>array("ido"=>$kinti_min_7d["timestamp"],1=>$kinti_min_7d["kinti_hom"],2=>$kinti_min_7d["kinti_para"])                               
                ,"benti_min_30d"=>array("ido"=>$benti_min_30d["timestamp"],1=>$benti_min_30d["benti_hom"],2=>$benti_min_30d["benti_para"])
                ,"kinti_min_30d"=>array("ido"=>$kinti_min_30d["timestamp"],1=>$kinti_min_30d["kinti_hom"],2=>$kinti_min_30d["kinti_para"])
                ,"benti_min_365d"=>array("ido"=>$benti_min_365d["timestamp"],1=>$benti_min_365d["benti_hom"],2=>$benti_min_365d["benti_para"])
                ,"kinti_min_365d"=>array("ido"=>$kinti_min_365d["timestamp"],1=>$kinti_min_365d["kinti_hom"],2=>$kinti_min_365d["kinti_para"])
                );
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
  	?>
    <h3>
      Last 1 day
    </h3>
    <div class="row">
      <div class="col-md-6">
        <? $this->showHomersekletBox("Bent MAX elmúlt 1d","glyphicon glyphicon-home",$data,"benti_max_1d");?>               
      </div>
      <div class="col-md-6">
        <? $this->showHomersekletBox("Kint MAX elmúlt 1d","glyphicon glyphicon-log-out",$data,"kinti_max_1d");?>      
      </div> 
    </div>
    
    <div class="row">
      <div class="col-md-6">
        <? $this->showHomersekletBox("Bent MIN elmúlt 1d","glyphicon glyphicon-home",$data,"benti_min_1d");?>               
      </div>
      <div class="col-md-6">
        <? $this->showHomersekletBox("Kint MIN elmúlt 1d","glyphicon glyphicon-log-out",$data,"kinti_min_1d");?>      
      </div> 
    </div>
    <h3>
      Last 7 days
    </h3>    
    <div class="row">
      <div class="col-md-6">
        <? $this->showHomersekletBox("Bent MAX elmúlt 7d","glyphicon glyphicon-home",$data,"benti_max_7d");?>               
      </div>
      <div class="col-md-6">
        <? $this->showHomersekletBox("Kint MAX elmúlt 7d","glyphicon glyphicon-log-out",$data,"kinti_max_7d");?>      
      </div>      
    </div>
    <div class="row">
      <div class="col-md-6">
        <? $this->showHomersekletBox("Bent MIN elmúlt 7d","glyphicon glyphicon-home",$data,"benti_min_7d");?>               
      </div>
      <div class="col-md-6">
        <? $this->showHomersekletBox("Kint MIN elmúlt 7d","glyphicon glyphicon-log-out",$data,"kinti_min_7d");?>      
      </div>            
    </div>  
    <h3>
      Last 30 days
    </h3>     
    <div class="row">
      <div class="col-md-6">
        <? $this->showHomersekletBox("Bent MAX elmúlt 30d","glyphicon glyphicon-home",$data,"benti_max_30d");?>               
      </div>
      <div class="col-md-6">
        <? $this->showHomersekletBox("Kint MAX elmúlt 30d","glyphicon glyphicon-log-out",$data,"kinti_max_30d");?>      
      </div>      
    </div>
    <div class="row">
      <div class="col-md-6">
        <? $this->showHomersekletBox("Bent MIN elmúlt 30d","glyphicon glyphicon-home",$data,"benti_min_30d");?>               
      </div>
      <div class="col-md-6">
        <? $this->showHomersekletBox("Kint MIN elmúlt 30d","glyphicon glyphicon-log-out",$data,"kinti_min_30d");?>      
      </div>            
    </div>  
    <h3>
      Last 365 days
    </h3>  
    <div class="row">
      <div class="col-md-6">
        <? $this->showHomersekletBox("Bent MAX elmúlt 365d","glyphicon glyphicon-home",$data,"benti_max_365d");?>               
      </div>
      <div class="col-md-6">
        <? $this->showHomersekletBox("Kint MAX elmúlt 365d","glyphicon glyphicon-log-out",$data,"kinti_max_365d");?>      
      </div>      
    </div>        
    <div class="row">
      <div class="col-md-6">
        <? $this->showHomersekletBox("Bent MIN elmúlt 365d","glyphicon glyphicon-home",$data,"benti_min_365d");?>               
      </div>
      <div class="col-md-6">
        <? $this->showHomersekletBox("Kint MIN elmúlt 365d","glyphicon glyphicon-log-out",$data,"kinti_min_365d");?>      
      </div>            
    </div>         
    <?
  } 
  function showHomersekletBox($heading,$icon_class,$data,$index){


  	?>
        <div class="panel panel-default">
            <div class="panel-heading"><?=$heading;?> <small><?=date("Y-m-d H:i:s",$data[$index]["ido"]);?></small></div>
            <div class="panel-body">
              <div class="row">
                <div class="col-sm-2 hidden-sm-down">
                 <span class="<?=$icon_class?>"></span>
                </div>
                <div class="col-sm-5">
                  <span class="homerseklet homerseklet<?=$this->getTemperatureCategory($data[$index][1])?>"><?=$data[$index][1]?>&#x2103;</span>
                  
                </div>
                <div class="col-sm-5">
                  <span class="humidity"><?=$data[$index][2]?>%</span>
                </div>
              </div>
             
            </div>
          </div>    
    <?
  }
   
}



