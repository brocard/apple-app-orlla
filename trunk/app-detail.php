<?php
set_time_limit(0);

require 'functions.php';

$trackId = $_REQUEST['trackId'];

$url = "http://itunes.apple.com/lookup?id=$trackId";

echo get_site_content($url);

//end  file 
