<?php
set_time_limit(0);

require 'functions.php'; 
require 'app_ids.php'; 

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
        //https://itunes.apple.com/us/app/tic-tac-toe/id289278457?mt=8 
        $reg_tag = '/\/id(\d*)\?/';
        $ret = preg_match_all($reg_tag, $url, $match_result);
        @$app_ids[$match_result[1][0]] += 1; 
    } 

    file_put_contents('app_ids.php', 
                      '<?php $app_ids = '. var_export($app_ids, TRUE) . ';');
}

$category_id = array(6018, 6000, 6022, 6017, 6016, 6015, 6023, 6014, 7001, 7002,
    7003, 7004, 7005, 7006, 7007, 7008, 7009, 7010, 7011, 7012, 7013, 7014, 7015,
    7016, 7017, 7018, 7019, 6013, 6012, 6020, 6011, 6010, 6009, 6021, 13007, 
    13006, 13008, 13009, 13010, 13011, 13012, 13013, 13014, 13015, 13002, 13017, 
    13018, 13003, 13019, 13020, 13021, 13001, 13004, 13023, 13024, 13025, 13026, 
    13027, 13005, 13028, 13029, 13030, 6008, 6007, 6006, 6005, 6004, 6003, 6002, 
    6001);

$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N',
    'O','P','Q','R','S','T','U','V','W','X','Y','Z','*');

$old = ''; //判断第一个运用，确定是否没有更多分页了。
foreach ($category_id as $cat_id) {
    foreach ($letters as $letter) {
        for ($i = 1; $i<500; $i++) {
            $url = "https://itunes.apple.com/us/genre/ios-games/id$cat_id?letter=$letter&page=$i"; 
            $page_content = get_site_content($url); 
            $urls = get_page_url($page_content); 

            if ($old == $urls[0]) {
                break;
            } else {
                $old = $urls[0]; 
            } 

            get_and_save_app_id($urls); //提取应用ID并保存

            //信息显示
            echo $url. "\n";
            echo 'App total: ' . count($app_ids)."\n";
        }
    }
}

echo 'finished!'."\n";

//end  file 
