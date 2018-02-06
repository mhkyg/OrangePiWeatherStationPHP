<?
class Weather_Timeline_Viewer extends Data_Viewer_Abstract_Page{

  function __construct($data_handler,$post){
    $this->data_handler = $data_handler;
    $this->post = $post;
	}
  
  function formatDate($timestamp){
  	return date("H:i",$timestamp);
  }

  function formatDateFull($timestamp){
  	return date("m-d H:i",$timestamp);
  }
  
  function getSunsetAndSunrise($timestamps){
    $res = array();
    $midnight = function ($item){
      return strtotime("midnight", $item);            	
    };
    $timestamps = array_map($midnight, $timestamps);
    $timestamps = array_unique($timestamps);
    $config = new View_Config();
    foreach ($timestamps as $timestamp) {
      $time = new DateTime(date("Y-m-d",$timestamp), new DateTimeZone('Europe/Budapest'));
      $zenith = $config->location["zenith"];
      $location_lat = $config->location["location_lat"];
      $location_long = $config->location["location_long"];
      if ($sunrise = date_sunrise($time->getTimestamp(),SUNFUNCS_RET_TIMESTAMP,$location_lat,$location_long,$zenith,$time->getOffset() / 3600)) {
        $res[] = array("timestamp"=>$sunrise,"text"=>"Napkelte");	
      }
      if ($sunset = date_sunset($time->getTimestamp(),SUNFUNCS_RET_TIMESTAMP,$location_lat,$location_long,$zenith,$time->getOffset() / 3600)) {
        $res[] = array("timestamp"=>$sunset,"text"=>"Napnyugta");	
      }      
      
    	
    }
    return $res;
    //return array("sunset"=>$sunset,"sunrise"=>$sunrise);  	
  }
  
  function show(){

    if (isset($this->post["day"]) and strtotime($this->post["day"]." 0:00" )!==false ) {
      $start = strtotime($this->post["day"]." 0:00" );
      $end = strtotime("+1 day",$start  );    	
    }else{
      $start = strtotime("-1 day");
      $end = mktime();
      $this->post["day"] = date("Y-m-d");
    }
    

    $plot_data = $this->getSunsetAndSunrise(array($start,$end) );
    
    //$plot_data = array(); 
    /*
    $sql="SELECT * FROM `homerseklet_adatok`  WHERE timestamp > ".$start." AND timestamp < ".$end." ORDER BY `homerseklet_adatok`.`id`" ;
    //echo $sql;
    $db=$this->database;
    $db->setQuery($sql);
    $res=$db->loadAssocList(null,"timestamp"); */
    
    $res = $this->data_handler->queryData("all","*",$start,$end,0,10000,"timestamp");
    /*
    if ($kezdo< strtotime("-1 day")) {
    	$categories = implode("\",\"", array_map(array($this,"formatDate") , array_keys($res))  );
    }else{
      $categories = implode("\",\"", array_map(array($this,"formatDate") , array_keys($res))  );
    }
    
    */
    $max_hom = 0;
    foreach ($res as $key=>$value) {
      $js_timestamp = $key."000";
    
      if (isset($value["kinti_hom"])) {
        $kint_hom[] = array($js_timestamp,$value["kinti_hom"]);	
      }else{
        if (null !== end($kint_hom)){
          $kint_hom[] = end($kint_hom);
        }         
      }
      
      if (isset($value["benti_hom"])) {
        $bent_hom[] = array($js_timestamp,$value["benti_hom"]);	
      }else{
        if (null !== (end($bent_hom))){
          $bent_hom[] = end($bent_hom);
        }
      }
      
      if (isset($value["kinti_para"])) {
        $kinti_para[] = array($js_timestamp,$value["kinti_para"]);	
      }else{
        if (null !== (end($kinti_para))){
          $kinti_para[] = end($kinti_para);
        }
      }
      
      if (isset($value["benti_para"])) {
        $benti_para[] = array($js_timestamp,$value["benti_para"]);	
      } else{
        if (null !== (end($benti_para))){
          $benti_para[] = end($benti_para);
        }
      }    
      if ($max_hom < $new_max = max($value["kinti_hom"], $value["benti_hom"]) ) {
      	$max_hom = $new_max;
      }
    }

    /*$kint_hom = implode(",",$kint_hom);
    $bent_hom = implode(",",$bent_hom);
    $kinti_para = implode(",",$kinti_para);
    $benti_para = implode(",",$benti_para);*/
  	?>
    <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    <form method="post">
      <input type="date" name="day" value="<?=$this->post["day"];?>">
      <input type="submit" value="Select day">      
    </form>
    <script type="text/javascript" language="JavaScript">
     $(function () {
    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });     
    $('#container').highcharts({
        title: {
            text: 'Hőmérséklet',
            x: -20 //center
        },
        subtitle: {
            text: 'Forrás: Saját adat gyűjtés',
            x: -20
        },
        xAxis: {
            type: 'datetime'
            ,plotLines: [<?=$this->getPlotlineString($plot_data)?>]
        },
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value} °C',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Hőmérséklet',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            opposite: true,
            max: <?=ceil($max_hom*1.1);?>
            
        }, { // Secondary yAxis
            gridLineWidth: 1,
            title: {
                text: 'Páratartalom',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value} %',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            max:100

        }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: 'Kinti hőmérséklet',
            yAxis: 0,
            data: jQuery.parseJSON('<?=json_encode($kint_hom,JSON_NUMERIC_CHECK);?>'),
            tooltip: {
                valueSuffix: ' °C'
            }
        }, {
            name: 'Benti hőmérséklet',
            yAxis: 0,
            data: jQuery.parseJSON('<?=json_encode($bent_hom,JSON_NUMERIC_CHECK);?>'),
            tooltip: {
                valueSuffix: ' °C'
            }
        }, {
            name: 'Benti páratartalom',
            yAxis: 1,
            
            data: jQuery.parseJSON('<?=json_encode($benti_para,JSON_NUMERIC_CHECK);?>'),
            tooltip: {
                valueSuffix: ' %'
            }
        }, {
            name: 'Kinti páratartalom',
            yAxis: 1,
            
            data: jQuery.parseJSON('<?=json_encode($kinti_para,JSON_NUMERIC_CHECK);?>'),
            tooltip: {
                valueSuffix: ' %'
            }
        }]
        ,  credits: {
          enabled: false
        }
    });

});
  
    </script>
    <?
  }
  /**
  $adatok = array("timestamp"=>[timestamp],"text"=>[string],[color=>string] )
  */
  function getPlotlineString($adatok){
    $res = array();
    
      foreach ($adatok as $key=>$value) {
        ob_start();
      	?>
        {
              color: '<?=$value["color"]?:("#ff0000") ?>', // Red
              width: 2,
              value: <?=$value["timestamp"]."000";?>
              ,label: {
                        text: '<?=$value["text"];?>'
                    }
            }
        <?
        $res[] = ob_get_clean();
      }
      
    return implode(",", $res);
  }
  
}