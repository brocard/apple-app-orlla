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
require 'functions.php'; 
$conn = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
mysql_select_db('apple_app', $conn) OR die( 1 );
mysql_query( "set character set 'utf8'" );
?>

  <div class="container">
      <div class="row">
        <div class="span3">
            <h3>Category of App</h3>
            <hr>
            <?php
            $mem = new Memcache; 
            $mem->connect('localhost', 12000) or die ("Could not connect"); 

            $sql = 'SELECT DISTINCT primaryGenreName FROM app';

            $app_category = $mem->get('app_category');
            if ( ! $app_category ) {
                $rows = mysql_query($sql, $conn); 
                $app_category = '<li><a href="./?">All</a>'; 
                while ( $row = mysql_fetch_array($rows) ) {
                    $app_category .= '<li><a href="?cat='.$row['primaryGenreName'].'">'.$row['primaryGenreName'].'</a>'; 
                } 

                $mem->set('app_category', $app_category, 0, 600); 
            } else {
                //echo 'hit';
            }

            echo $app_category; 
            ?> 
        </div><!--end span3-->

        <div class="span9">


<?php 

$cat = (isset($_REQUEST['cat']) AND $_REQUEST['cat'] != '') ? $_REQUEST['cat'] : ''; 

$where = ($cat == '' ? '' : " WHERE primaryGenreName='$cat' ");

$order = $_REQUEST['order']; 
$order_by = '';
if ($order == 'date') { 
    $order_by = " ORDER BY releaseDate DESC";
} else if ($order == 'price') { 
    $order_by = " ORDER BY formattedPrice DESC";
} else if ($order == 'rating') { 
    $order_by = " ORDER BY averageUserRating DESC";
}

$sql  = "SELECT count(*) AS total FROM app $where";
$rows = mysql_query($sql, $conn);
$total = mysql_fetch_array($rows);

echo '<p>Sum of App : ' . $total['total'];

$perpage = 10;
$page = $_REQUEST['pg'] ? $_REQUEST['pg'] : 1;

$sql  = "SELECT * FROM app $where $order_by " . build_limit($page, $perpage); 

$mem_key = 'w'.$cat.$order.$page;
$cat_list = $mem->get($mem_key);
if ( ! $cat_list) { 
    //echo 'miss';
    $rows = mysql_query($sql, $conn); 
    $cat_list = '<table class="table">'; 
    
    $cat_list .= '<tr>';
    $cat_list .= '<td></td>';
    $cat_list .= '<td><a href="./?cat='.$cat.'&order=date">Release Date</a></td>';
    $cat_list .= '<td></td>';
    $cat_list .= '<td><a href="./?cat='.$cat.'&order=price">Price</></td>';
    $cat_list .= '<td><a href="./?cat='.$cat.'&order=rating">Rating</a></td>';
    $cat_list .= '</tr>';
    while ( $row = mysql_fetch_array($rows) ) {
        $cat_list .= '<tr>';
        $cat_list .= '<td><a><img class="thumbnail" width="57px" height="57px" src="'.$row['artworkUrl60'].'" alt="" /></a></td>';
        $cat_list .= '<td>';
        $cat_list .= '<p><a href="app-detail.php?id='.$row['trackId'].'">'.$row['trackName'].'</a></p>';
        $cat_list .= '<p>Release Date: ' . $row['releaseDate'];
        $cat_list .= '</td>';
        $cat_list .= '<td>'.$row['primaryGenreName'].'</td>';
        $cat_list .= '<td>'.$row['formattedPrice'].'</td>';
        $cat_list .= '<td>'.$row['averageUserRating'].'</td>'; 
        $cat_list .= '</tr>';
    } 
    $cat_list .= '</table>';
    $mem->set($mem_key, $cat_list, 0, 600); 
} else {
    //echo 'hit';
}

echo $cat_list;

$url  = './?'. ($cat   != '' ? 'cat='   . $cat   .'&' : '') 
             . ($order != '' ? 'order=' . $order .'&' : '') . 'pg=__page__';

echo build_pagebar($total['total'], $perpage, $page, $url);

echo <<< html
        </div><!--end span9-->
      </div><!--end row-->
    </div> <!-- /container -->
    <script>
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
</body>
</html>
html;

//end  file 
