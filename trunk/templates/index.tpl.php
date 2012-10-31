<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?=$title?></title>
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

      <div class="row">
        <div class="span3">
            <ul class="nav nav-tabs">
                <li<?=($app_tb == 'app'  ? ' class="active" ' : '')?>><a href="./?app_tb=app">apple</a>
                <?php 
                if ($app_tb == 'papa') {
                    if (isset($_REQUEST['tag'])) {
                        echo '<li><a href="./?app_tb=papa">papa</a>';
                        echo '<li class="active"><a href="./?tag=">tags</a>'; 
                    } else {
                        echo '<li class="active"><a href="./?app_tb=papa">papa</a>';
                        echo '<li><a href="./?tag=">tags</a>'; 
                    }
                } else {
                    echo '<li><a href="./?app_tb=papa">papa</a>';
                }
                ?>
            </ul>

            <ul class="nav nav-list"> 
            <?=$left_nav?>
            </ul>
        </div><!--end span3-->

        <div class="span9"> 
            <form class="form-search" action="" method="get">
                <div class="input-append">
                <input type="hidden" value="<?php echo $cat; ?>" name="cat"> 
                <input type="text" value="<?php echo $search_key; ?>" name="search_key" class="span3 search-query">
                <button type="submit" class="btn">Search</button>
                </div>
            </form>

            <p>Sum of App : <?=$total['total']?>
            <? if ($search_key != '' AND $app_tb == 'app'): ?>
            <span style="margin-left:30px;"><a target="_blank" href="./papa_api.php?op=add_search_to_papa&cat=<?=$cat?>&search=<?=$search_key?>">Add to PaPa</a></span>
            <? endif; ?> 

            <table class="table"> 
                <tr>
                    <td></td>
                    <td><a href="./?<?=$url_param?>order=date">Release Date</a></td>
                    <td></td>
                    <td><a href="./?<?=$url_param?>order=price">Price</a></td>
                    <td><a href="./?<?=$url_param?>order=rating">Rating</a></td>
                </tr>

                <? foreach ( $rows_app as $row ): ?> 
                <tr>
                    <td><a><img class="thumbnail" width="57px" height="57px" src="<?=$row['artworkUrl60']?>" alt="" /></a></td>
                    <td>
                        <p><a href="app-detail.php?id=<?=$row['trackId']?>"><?=$row['trackName']?></a></p>
                        <p>Release Date: <?=$row['releaseDate']?></p>
                    </td> 
                    <td>
                        <p><?=$row['primaryGenreName']?>
                        <? if ($app_tb == 'app'): ?>
                        <p><button onclick="add_to_papa(this, <?=$row['trackId']?>);">Add to PaPa</button>
                        <? endif; ?> 
                    </td> 
                    <td><?=$row['formattedPrice']?></td>
                    <td><?=$row['averageUserRating']?></td>
                </tr> 
                <? endforeach; ?> 
            </table>

            <?=$pagebar?> 

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
