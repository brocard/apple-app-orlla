<?php
set_time_limit(0);

require 'functions.php';

$keyword = $_REQUEST['keyword'];
$limit = $_REQUEST['limit'];

$url = "http://itunes.apple.com/search?term=$keyword&limit=$limit&media=software";

echo get_site_content($url);

//end  file 
