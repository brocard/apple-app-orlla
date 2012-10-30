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

    $trackId = (isset($_REQUEST['id']) AND $_REQUEST['id'] != '') ? $_REQUEST['id'] : ''; 

    $sql  = "SELECT * FROM $app_tb Where trackId='$trackId'";
    $rows = mysql_query($sql, $conn); 
    $row = mysql_fetch_array($rows); 
    ?>

  <div class="container">
      <div class="row">
        <div class="span12"> 
            <ul class="breadcrumb">
            <li><a href="/itune">Home</a> <span class="divider">/</span></li>
            <li><a href="/itune/?cat=<?=$row['primaryGenreName']?>"><?=$row['primaryGenreName']?></a> <span class="divider">/</span></li>
            <li class="active"><?=$row['trackName']?></li>
            </ul>
        </div>
      
      </div>

      <div class="row">
        <div class="span9"> 
        <?php 

        echo '<table class="table">'; 
        echo '<tr>';
        echo '<td width="1px;"><a><img width="256px" height="256px" src="'.$row['artworkUrl512'].'" alt="" /></a></td>';
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
        echo '<h2>Description</h2><p>'.escape_html($row['description']);

        $screen_shot = json_decode($row['ipadScreenshotUrls']);
        if (count($screen_shot) > 0) {
            echo '<h2>iPad Screen Shot</h2>';
            foreach ($screen_shot as $img_url) {
                echo '<img width="512" src="'. $img_url .'" />';
            } 
        }

        $screen_shot = json_decode($row['screenshotUrls']);
        if (count($screen_shot) > 0) {
            echo '<h2>Screen Shot</h2>';
            foreach ($screen_shot as $img_url) {
                echo '<img width="512" src="'. $img_url .'" />';
            } 
        }

        echo '</td>';
        echo '</tr>'; 

        echo '</table>';
        ?> 
        </div><!--end span9-->

        <div class="span3"> 
        <?php
            if ($app_tb == 'papa') {
                $sql   = "SELECT * FROM humit Where trackId='$trackId'";
                $rows  = mysql_query($sql, $conn); 
                $app_humit = mysql_fetch_array($rows); 
            }

            $app_humit['age'] = explode(',', $app_humit['age']);
            foreach ($app_humit['age'] as $key => $age) {
                $app_humit['age'][$key] = trim($age);
            } 

            $humit_category = array('Musical', 'Bodily Kinesthetic', 'Logical Mathematical', 'Linguistic', 'Spatial', 'Interpersonal', 'Intrapersonal', 'Naturalist', 'Existential');
            $humit_age      = array('0-1','1','2','3','4','5','5+','6','6+','7','7+','8+'); 
        ?> 
            <div style="<?=($app_tb == 'app' ? 'display:none;' : '')?>border:1px solid #ddd;padding:10px;">
                <h3>Humit Info</h3>

                <form class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">Category:</label>
                        <div class="controls">
                        <select class="span1" id="humit">
                        <?php
                        foreach ($humit_category as $cat) {
                            echo '<option '.($app_humit['humit'] == $cat ? 'selected' : '').' value="'.$cat.'">'.$cat.'</option>'."\n";
                        }
                        ?>
                        </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Gender: </label>
                        <div class="controls">
                        <select class="span1" id="gender">
                            <option <?=($app_humit['gender'] == 'Both'  ? 'selected' : '')?> value="Both">Both</option>
                            <option <?=($app_humit['gender'] == 'Boys'  ? 'selected' : '')?> value="Boys">Boys</option>
                            <option <?=($app_humit['gender'] == 'Girls' ? 'selected' : '')?> value="Girls">Girls</option>
                        </select>
                        </div>
                    </div>

                    <div class="control-group"> 
                        <label class="control-label">Age: </label>
                        <div class="controls">
                        <?php
                        foreach ($humit_age as $age) {
                            echo '<input '.( in_array($age, $app_humit['age']) ? 'checked="checked"' : '').' type="checkbox" name="age" value="'.$age.'"> '.$age.'<br />';
                        }
                        ?>
                        </div>
                    </div>

                    <div class="control-group"> 
                        <div class="controls">
                        <span class="btn" onclick="humit_save(this, <?=$row['trackId']?>);">Save</span>
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
    function humit_save(obj, trackId) {
        var humit  = $('#humit').val();
        var gender = $('#gender').val();
        var age    = [];  
        $('input[name="age"]:checked').each(function(){  
            age.push($(this).val());  
        });  

        if (age.length == 0) {
            alert('Selecting ages please！');  
            return false;
        } else if (age.length > 3) {
            alert("Age option can't more than 3");  
            return false;
        } 

        $.get('papa_api.php', {op : 'humit_info_save', trackId : trackId, humit : humit, gender : gender, age : age}, function(msg) {
            if ($.trim(msg) == 'ok') {
                $(obj).html('Saved'); 
            } else {
                alert('save failure!');
                console.log(msg);
            }
        });
    }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
</body>
</html> 
