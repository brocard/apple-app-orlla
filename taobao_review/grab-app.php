<?php
set_time_limit(0);

require '../functions.php'; 

/**
 * 取得页面里，贴子的URL
 */
function get_page_url($web_content) { 
    //利用正则表达式得到图片链接
    $reg_tag = '/<a href=\"(.*)mt=8\">/';
    $ret = preg_match_all($reg_tag, $web_content, $match_result);
    
    return $match_result[1]; 
}

function get_and_save_app_id($urls) {
    global $app_ids;

    foreach ($urls as $url) {
        $reg_tag = '/\/id(\d*)\?/';
        $ret = preg_match_all($reg_tag, $url, $match_result);
        @$app_ids[$match_result[1][0]] += 1; 
    } 

}

$url = "http://detail.tmall.com/item.htm?id=16288188679";

//Tmall商品 
//$url = "http://rate.tmall.com/list_detail_rate.htm?itemId=16288188679";  
//$url = "http://rate.tmall.com/list_detail_rate.htm?itemId=19024700425&order=0&append=0&currentPage=1";  

//$url = "http://rate.tmall.com/list_detail_rate.htm?itemId=16288188679&spuId=&sellerId=579603181&order=0&forShop=1&append=0&currentPage=1";

//taobao商品
$url = "http://rate.taobao.com/feedRateList.htm?userNumId=186778114&auctionNumId=17022559073&siteID=7&currentPageNum=1&rateType=1&orderType=sort_weight&showContent=1&attribute=";

//$page_content = '{'.get_site_content($url).'}';  //Tmall

$page_content = get_site_content($url); 

$page_content = iconv('gbk','utf-8', $page_content);
$page_content = substr($page_content, 5, strlen($page_content)-8); //taobao

print_a(json_decode($page_content, true));

//var_dump(json_last_error()); 

//$urls = get_page_url($page_content); 

//end  file 
