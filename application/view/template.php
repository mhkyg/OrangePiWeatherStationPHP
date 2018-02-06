<?
class Weather_Main_Template{

	function __construct($inner_page){
		$this->inner_page = $inner_page;
	}
  
  function show(){
  	?>
    <!DOCTYPE html>
    <html lang="hu">
      <head>
        <meta charset="UTF-8">
        <title>Temperature</title>
        <link rel="stylesheet" href="/css/bootstrap.min.css"  crossorigin="anonymous">
        <link rel="stylesheet" href="/css/bootstrap-theme.min.css"  crossorigin="anonymous">
        <link rel="stylesheet" href="/css/style.css" >
        <script src="/js/jquery.js"  crossorigin="anonymous"></script>
        <script src="/js/bootstrap.min.js"  crossorigin="anonymous"></script>
        <script src="/js/highcharts.js" crossorigin="anonymous"></script>
        
      </head>
     <body>
      <nav class="navbar navbar-default">
        <div class="container-fluid">

          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
              <li ><a href="/">Current <span class="sr-only">(current)</span></a></li>
              <li><a href="?page=timeline">Timeline</a></li>
              <li><a href="?page=statistics">Statistics</a></li>

            </ul>
            
            
          </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
      </nav>     
     <div class="container">
      
      <?
      $this->inner_page->show();
      ?>
       </div>
     </body>
    </html>            
    <?
  }
