<?php
require 'config.php'; 
require 'functions.php'; 

require('./templates/class_template.php');    
$path = './templates/';    
$tpl = & new Template($path);    

$app_tb   = select_app_tb();

$mem = new Memcache; 
$mem->connect('localhost', 12000) or die ("Could not connect"); 
$miss_hit = false;      //是否显示memcache命中信息

$tag = $_REQUEST['tag']; 
$cat = (isset($_REQUEST['cat']) AND $_REQUEST['cat'] != '') ? $_REQUEST['cat'] : ''; 
$search_key = $_REQUEST['search_key']; 

//left nav
$left_nav = '';
if (isset($_REQUEST['tag'])) {
    $sql = "SELECT * FROM tags"; 
    $rows = mysql_query($sql, $conn); 
    while ( $row = mysql_fetch_array($rows) ) {
        $left_nav .= '<li'.($tag == $row['tag'] ? ' class="active" ' : '').'>';
        $left_nav .= '<a href="?tag='.$row['tag'].'">' .$row['tag'].'</a>'; 
    } 

    $left_nav .= '<br/><input id="new_tag" type="text" class="input-medium"><br />';
    $left_nav .= '<span class="btn" onclick="add_tag();">Add Tag</span>'; 

} else { 
    $sql = "SELECT DISTINCT primaryGenreName FROM $app_tb"; 
    $mem_key = $app_tb.'_cat_'.$cat; 
    $app_category = $mem->get($mem_key);
    if ( ! $app_category ) {
        if ($miss_hit) echo '<p>cat miss';
        $rows = mysql_query($sql, $conn); 

        $app_category = '<li'. ($cat==''? ' class="active"':'') .'><a href="./?">All</a>'; 
        while ( $row = mysql_fetch_array($rows) ) {
            $app_category .= '<li'.($cat==$row['primaryGenreName'] ? ' class="active"':'') .'>';
            $app_category .= '<a href="?cat='.$row['primaryGenreName'].'">' .$row['primaryGenreName'].'</a>'; 
        } 

        $mem->set($mem_key, $app_category, 0, 600); 
    } else {
        if ($miss_hit) echo '<p>cat hit';
    }

    $left_nav = $app_category; 
} 


//Sum of App
if (isset($_REQUEST['tag'])) {
    if ($tag == '') {
        //
    } else {
        $sql  = "SELECT * FROM tags WHERE tag = '$tag'"; 
        $rows = mysql_query($sql, $conn); 
        $tag_id = mysql_fetch_array($rows);
        $tag_id = $tag_id['id'];

        $sql  = "SELECT COUNT(*) AS total FROM tag_for_app WHERE tag_id = $tag_id"; 
        $rows = mysql_query($sql, $conn); 
        $total = mysql_fetch_array($rows);
    }
} else {
    $where = ($cat == '' ? '' : " WHERE primaryGenreName='$cat' ");
    if ($search_key != '') {
        if ($where == '') {
            $where = " WHERE MATCH (trackName) AGAINST ('$search_key') ";
        } else {
            $where .= " AND MATCH (trackName) AGAINST ('$search_key') "; 
        }
    } 

    $sql  = "SELECT count(*) AS total FROM $app_tb $where";

    $mem_key = $app_tb.'_sum_'.$cat.$search_key;
    $total = $mem->get($mem_key);
    if ( ! $total) { 
        if ($miss_hit) echo '<p>cat miss';
        $rows = mysql_query($sql, $conn);
        $total = mysql_fetch_array($rows);
        $mem->set($mem_key, $total, 0, 600); 
    } else {
        if ($miss_hit) echo '<p>cat hit';
    }

}

//app list
$perpage = 10;
$page = $_REQUEST['pg'] ? $_REQUEST['pg'] : 1;

$url_param = '';
if ($cat != '') {
    $url_param .= 'cat='.$cat.'&'; 
}

if ($search_key != '') {
    $url_param .= 'search_key='.$search_key.'&'; 
}

if (isset($_REQUEST['tag'])) {
    if ($tag == '') {
        //
    } else {
        $sql  = "SELECT * FROM papa WHERE trackId IN "
              . "(SELECT trackId FROM tag_for_app WHERE tag_id = $tag_id) " 
              . build_limit($page, $perpage); 
        $rows_app = mysql_query($sql, $conn); 

        $url  = './?'. ($cat        != '' ? 'cat='        . $cat   .'&' : '') 
                    . ($search_key != '' ? 'search_key=' . $search_key .'&' : '') 
                    . ($order      != '' ? 'order='      . $order .'&' : '') . 'pg=__page__';
        $pagebar = build_pagebar($total['total'], $perpage, $page, $url); 
    }
    
} else {
    $order = $_REQUEST['order']; 
    $order_by = '';
    if ($order == 'date') { 
        $order_by = " ORDER BY releaseDate DESC";
    } else if ($order == 'price') { 
        $order_by = " ORDER BY formattedPrice DESC";
    } else if ($order == 'rating') { 
        $order_by = " ORDER BY averageUserRating DESC";
    }

    $sql  = "SELECT * FROM $app_tb $where $order_by " . build_limit($page, $perpage); 
    $mem_key = $app_tb.'_list_'.$cat.$order.$page.$search_key;
    $rows_app = $mem->get($mem_key);
    if ( ! $rows_app) { 
        if ($miss_hit) echo '<p>cat miss';
        $rows_app = mysql_query($sql, $conn); 
        $mem->set($mem_key, $rows_app, 0, 600); 
    } else {
        if ($miss_hit) echo '<p>cat hit';
    } 

    $url  = './?'. ($cat        != '' ? 'cat='        . $cat   .'&' : '') 
                . ($search_key != '' ? 'search_key=' . $search_key .'&' : '') 
                . ($order      != '' ? 'order='      . $order .'&' : '') . 'pg=__page__';

    $pagebar = build_pagebar($total['total'], $perpage, $page, $url); 
} 

$tpl->set('title', 'index');    
$tpl->set('app_tb', $app_tb);    
$tpl->set('left_nav', $left_nav);    

$tpl->set('cat', $cat);    
$tpl->set('search_key', $search_key);    
$tpl->set('total', $total);    

$tpl->set('url_param', $url_param);    
$tpl->set('rows_app', app_list($rows_app));    
$tpl->set('pagebar', $pagebar);    


echo $tpl->fetch('index.tpl.php');    

function app_list($rows) {
    global $cat;
    global $search_key;
    $app_list = array();

    while ( $row = mysql_fetch_array($rows) ) { 
        if ($search_key != '') {
            $row['trackName'] = str_ireplace($search_key, "<b>$search_key</b>", $row['trackName']);
        } 

        $app_list[] = $row;
    }

    return $app_list;
}

//end file
