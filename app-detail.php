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

    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .hero-unit {
          padding:30px;
      }
      .controls {
          margin-left: 100px !important;
      }
      .control-label {
          width: 80px !important;
      }
    </style>
  </head>

  <body> 

    <?php 
    require 'config.php'; 
    require 'functions.php'; 
    $app_tb = select_app_tb();

    $conn = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
    mysql_select_db('apple_app', $conn) OR die( 1 );
    mysql_query( "set character set 'utf8'" );
    ?>

  <div class="container">
      <div class="row">
        <div class="span9"> 
        <?php 
        $trackId = (isset($_REQUEST['id']) AND $_REQUEST['id'] != '') ? $_REQUEST['id'] : ''; 

        $sql  = "SELECT * FROM $app_tb Where trackId='$trackId'";
        $rows = mysql_query($sql, $conn); 

        echo '<table class="table">'; 
        $row = mysql_fetch_array($rows);
        echo '<tr>';
        echo '<td width="1px;"><a><img width="512px" height="512px" src="'.$row['artworkUrl512'].'" alt="" /></a></td>';
        echo '<td>';
        echo '<h2>'.$row['trackName'].'</h2>';
        echo '<p>Category : '.$row['primaryGenreName'].'</p>';
        echo '<p>Kind : '.$row['kind'].'</p>';
        echo '<p>Price : '.$row['formattedPrice'].'</p>';
        echo '<p>User Rating : '.$row['averageUserRating'].'</p>';
        echo '<p>Version : '.$row['version'].'</p>';
        echo '<p>Release Date : '.$row['releaseDate'].'</p>';
        echo '<p>Supported Devices : '.str_replace(array('[', ']', '"', ','), array('', '', '',', '),$row['supportedDevices']).'</p>';
        echo '<p>Release Notes : '.$row['releaseNotes'].'</p>';
        echo '</td>'; 
        echo '</tr>'; 
        echo '<tr>'; 
        echo '<td colspan="2">';
        echo '<h2>Description</h2>'.escape_html($row['description']);

        $screen_shot = json_decode($row['ipadScreenshotUrls']);
        if (count($screen_shot) > 0) {
            echo '<h2>iPad Screen Shot</h2>';
            foreach ($screen_shot as $img_url) {
                echo '<img src="'. $img_url .'" />';
            } 
        }

        $screen_shot = json_decode($row['screenshotUrls']);
        if (count($screen_shot) > 0) {
            echo '<h2>Screen Shot</h2>';
            foreach ($screen_shot as $img_url) {
                echo '<img src="'. $img_url .'" />';
            } 
        }

        echo '</td>';
        echo '</tr>'; 

        echo '</table>';
        ?> 
        </div><!--end span9-->

        <div class="span3"> 
            <div style="border:1px solid #ddd;padding:10px;">
                <h3>Humit Info</h3>

                <form class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">Category:</label>
                        <div class="controls">
                        <select class="span1">
                            <option value="Brain">Brain</option>
                            <option value="Puzzle">Puzzle</option>
                            <option value="Math">Math</option>
                        </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Gender: </label>
                        <div class="controls">
                        <select class="span1">
                            <option value="Both">Both</option>
                            <option value="Boys">Boys</option>
                            <option value="Girls">Girls</option>
                        </select>
                        </div>
                    </div>

                    <div class="control-group"> 
                        <label class="control-label">Age: </label>
                        <div class="controls">
                        <input type="checkbox"> 0-1 <br />
                        <input type="checkbox"> 1 <br />
                        <input type="checkbox"> 2 <br />
                        <input type="checkbox"> 3 <br />
                        <input type="checkbox"> 4 <br />
                        <input type="checkbox"> 5 <br />
                        <input type="checkbox"> 6 <br />
                        <input type="checkbox"> 7 <br />
                        <input type="checkbox"> 8 <br />
                        <input type="checkbox"> 9+ <br />
                        </div>
                    </div>

                    <div class="control-group"> 
                        <div class="controls">
                        <button class="btn" onclick="humit_save();">Save</button>
                        </div>
                    </div>

                </form>
            </div>

            <div style="margin-top:10px;border:1px solid #ddd;padding:10px;">
                <p>ad. info
                <p>1
                <p>2
                <p>3
                <p>4
                <p>5
                <p>6
            </div>
        </div><!--end span3-->

      </div><!--end row-->
    </div> <!-- /container -->

    <script>
    function humit_save() {
        alert('save');
        return;
    }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
</body>
</html> 
