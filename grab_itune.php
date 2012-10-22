<?php
set_time_limit(0);

/**
 * 抓取网页内容
 */
function get_site_content($site_url) { 
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $site_url); 
    curl_setopt($curl, CURLOPT_HEADER, FALSE); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 20);  //设置超时
    $data = curl_exec($curl); 
    curl_close($curl);

    return $data; 
}

$keyword = $_REQUEST['keyword'];
$limit = $_REQUEST['limit'];

$url = "http://itunes.apple.com/search?term=$keyword&limit=$limit&media=software";

echo get_site_content($url);

//end  file 
