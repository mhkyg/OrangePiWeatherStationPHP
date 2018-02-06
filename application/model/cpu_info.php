<?
class CPU_Info{
  /**
   *return array("load"=>(float),"cpus"=>(int)
   */
	function getServerLoad(){
      $os=strtolower(PHP_OS);
      if(strpos($os, 'win') === false){
            if(is_readable('/proc/loadavg')){
                $load = trim(file_get_contents('/proc/loadavg'));
                
                $load = explode(' ', $load);

                $load = $load[0];
            }

            if(!$load and function_exists('shell_exec')){
                $load = explode(' ', `uptime`);
                $load = $load[count($load)-3];
            }
          if(function_exists('shell_exec'))
              $cpu_count = shell_exec('cat /proc/cpuinfo | grep "^processor" | wc -l');        
      }else{
          if(class_exists('COM')){
              $wmi=new COM('WinMgmts:\\\\.');
              $cpus=$wmi->InstancesOf('Win32_Processor');
              
              $cpu_count=0;
  
              foreach($cpus as $cpu){
                if ($load===null) {
                	$load=0;
                }
                $load += ($cpu->LoadPercentage/100);
                $cpu_count++;
              }
          }
      }
      if ($load===null ) {
      	return null;
      }

      return array('load'=>$load, 'cpus'=>$cpu_count);
  }
}