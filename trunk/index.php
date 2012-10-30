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
        padding-top: 60px !important;
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
  <div class="container">
      <div class="row">
        <div class="span12">
        </div>
      </div>

<?php 
require 'config.php'; 
require 'functions.php'; 

$app_tb   = select_app_tb();

$conn = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
mysql_select_db('apple_app', $conn) OR die( 1 );
mysql_query( "set character set 'utf8'" );

$mem = new Memcache; 
$mem->connect('localhost', 12000) or die ("Could not connect"); 
$miss_hit = false;      //是否显示memcache命中信息

$tag = $_REQUEST['tag']; 
$cat = (isset($_REQUEST['cat']) AND $_REQUEST['cat'] != '') ? $_REQUEST['cat'] : ''; 
$search_key = $_REQUEST['search_key'];
?> 
      <div class="row">
        <div class="span3">
            <?php include 'left_nav.tpl.php'; ?> 
        </div><!--end span3-->

        <div class="span9"> 
            <form class="form-search" action="" method="get">
                <div class="input-append">
                <input type="hidden" value="<?php echo $cat; ?>" name="cat"> 
                <input type="text" value="<?php echo $search_key; ?>" name="search_key" class="span3 search-query">
                <button type="submit" class="btn">Search</button>
                </div>
            </form>
<?php 
if (isset($_REQUEST['tag'])) {
    if ($tag == '') {
        //
    } else {
        $sql  = "SELECT * FROM tags WHERE tag = '$tag'"; 
        $rows = mysql_query($sql, $conn); 
        $tag_id = mysql_fetch_array($rows);
        $tag_id = $tag_id['id'];

        $sql  = "SELECT COUNT(*) AS total FROM tag_for_app WHERE tag_id = $tag_id"; 
        $rows = mysql_query($sql, $conn); 
        $total = mysql_fetch_array($rows);
        echo '<p>Sum of App : ' . $total['total'];
    }
} else {
    $where = ($cat == '' ? '' : " WHERE primaryGenreName='$cat' ");
    if ($search_key != '') {
        if ($where == '') {
            $where = " WHERE MATCH (trackName) AGAINST ('$search_key') ";
        } else {
            $where .= " AND MATCH (trackName) AGAINST ('$search_key') "; 
        }
    } 

    $sql  = "SELECT count(*) AS total FROM $app_tb $where";

    $mem_key = $app_tb.'_sum_'.$cat.$search_key;
    $total = $mem->get($mem_key);
    if ( ! $total) { 
        if ($miss_hit) echo '<p>cat miss';
        $rows = mysql_query($sql, $conn);
        $total = mysql_fetch_array($rows);
        $mem->set($mem_key, $total, 0, 600); 
    } else {
        if ($miss_hit) echo '<p>cat hit';
    }

    echo '<p>Sum of App : ' . $total['total'];
    if ($search_key != '' and $app_tb == 'app') {
        echo '<span style="margin-left:30px;"><a target="_blank" href="./papa_api.php?op=add_search_to_papa&search=' . $search_key . '">Add to PaPa</a></span>';
    } 
}

include 'app_list.tpl.php';

?> 
        </div><!--end span9-->
      </div><!--end row-->
    </div> <!-- /container -->
    <script>
    function add_to_papa(obj, trackId) {
        $.get('./papa_api.php', {op: 'add_to_papa', trackId: trackId}, function(msg) {
            if ($.trim(msg) == 'ok') { 
                $(obj).html('Add Ok'); 
            } else {
                alert('运用加入PaPa不成功！稍后再试。');
                console.log(msg);
            }
        }); 
    }

    function add_tag() {
        var tag = $('#new_tag').val();

        $.get('./papa_api.php', {op: 'add_tag', tag : tag}, function(msg) {
            if ($.trim(msg) == 'ok') { 
                location.reload();
            } else {
                alert('加入tag不成功！稍后再试。');
                console.log(msg);
            }
        });

    }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
</body>
</html> 
