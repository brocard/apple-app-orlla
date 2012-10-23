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

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h3>abc</h3>
        <h1>abc</h1>
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span4">
            <div class="control-group">
                <label class="control-label" for="keyword">关键字</label>
                <div class="controls">
                <input type="text" id="keyword" placeholder="education">
                <input type="text" id="limit" value="10" placeholder="num">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button class="btn" onclick="itune_search();">搜索</button>
                </div>
            </div> 
        </div>
      </div>
      <div id="result">123</div>

      <hr>

      <footer>
        <p>footer</p>        
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
    <script>
        function itune_search() {
            var keyword = $('#keyword').val();
            var limit = $('#limit').val();

            $.getJSON('grab-itune.php', {keyword:keyword,limit:limit}, function(msg){

                var html = 'result Count: ';
                html += msg.resultCount;
                html += '<br/>';

                $.each(msg.results, function(i,item){
                    html += '<div style="border:1px solid #DDD;padding:10px;margin:5px 0px;">';
                    html += '<button class="btn" onclick="add_to_db(this, ' + item['trackId'] + ');">收录</button>'; 

                    for (prop in item) {
                        html += "<p><b>"+prop+"</b>" + ":" + item[prop] + '</p>';
                    } 

                    html += '</div>';

                });


                $('#result').html(html); 
            });
            
        } 

        function add_to_db(obj, trackId) {
            $.get('add_to_db.php', {trackId:trackId}, function(msg){

                if ( msg=='ok' ) {
                    $(obj).html('已录'); 
                } else {
                    alert(msg);
                } 

            });
        }

    </script>

  </body> 
</html> 
