<?php

/**
 * 抓取网页内容
 */
function get_site_content($url) { 
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $url); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
    curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, 1);
    curl_setopt($curl, CURLOPT_HEADER, FALSE); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);  //设置超时

    $data = curl_exec($curl); 
    curl_close($curl);

    return $data; 
}


//end  file 
