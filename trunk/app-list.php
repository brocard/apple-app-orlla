<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>abc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .hero-unit {
          padding:30px;
      }
    </style>
    <link href="css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
  </head>

  <body> 

<?php 
require 'config.php';

$conn = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
mysql_select_db('apple_app', $conn) OR die( 1 );
mysql_query( "set character set 'utf8'" );

$sql  = "SELECT * FROM app";
$rows = mysql_query($sql, $conn);

while ( $row = mysql_fetch_array($rows) ) {
    echo '<div style="padding:10px;margin:10px 0px;border:1px solid #DDD;">';
    echo $row['trackId'];
    echo '<button onclick="detail(this, '.$row['trackId'].');">detail</button>';
    echo '</div>';
} 
echo <<< html
<script>
function detail(obj, trackId) {
    $.getJSON('app-detail.php', {trackId:trackId}, function(msg){
        console.log(msg);

        //var html = 'result Count: ';
        //html += msg.resultCount;
        //html += '<br/>';

        //$.each(msg.results, function(i, item){ 
        //    for (prop in item) {
        //        html += "<p><b>"+prop+"</b>" + ":" + item[prop] + '</p>';
        //    } 

        //}); 

        //$(obj).parent().html(html); 
    });
}
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
</body>
</html>
html;

//end  file 
