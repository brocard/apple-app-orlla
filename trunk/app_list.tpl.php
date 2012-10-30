<?php
$perpage = 10;
$page = $_REQUEST['pg'] ? $_REQUEST['pg'] : 1;

if (isset($_REQUEST['tag'])) {
    if ($tag == '') {
        //
    } else {
        $sql  = "SELECT * FROM papa WHERE trackId IN "
              . "(SELECT trackId FROM tag_for_app WHERE tag_id = $tag_id) " 
              . build_limit($page, $perpage); 
        $rows = mysql_query($sql, $conn); 
        echo app_list($rows); 
        echo build_pagebar($total['total'], $perpage, $page, $url); 
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
    $cat_list = $mem->get($mem_key);
    if ( ! $cat_list) { 
        if ($miss_hit) echo '<p>cat miss';
        $rows = mysql_query($sql, $conn); 
        $cat_list = app_list($rows);
        $mem->set($mem_key, $cat_list, 0, 600); 
    } else {
        if ($miss_hit) echo '<p>cat hit';
    }

    echo $cat_list;

    $url  = './?'. ($cat        != '' ? 'cat='        . $cat   .'&' : '') 
                . ($search_key != '' ? 'search_key=' . $search_key .'&' : '') 
                . ($order      != '' ? 'order='      . $order .'&' : '') . 'pg=__page__';

    echo build_pagebar($total['total'], $perpage, $page, $url); 
} 

function app_list($rows) {
    global $cat;
    global $search_key;

    $cat_list = '<table class="table">'; 

    $url_param = '';
    if ($cat != '') {
        $url_param .= 'cat='.$cat.'&'; 
    }
    if ($search_key != '') {
        $url_param .= 'search_key='.$search_key.'&'; 
    }
    
    $cat_list .= '<tr>';
    $cat_list .= '<td></td>';
    $cat_list .= '<td><a href="./?'.$url_param.'order=date">Release Date</a></td>';
    $cat_list .= '<td></td>';
    $cat_list .= '<td><a href="./?'.$url_param.'order=price">Price</></td>';
    $cat_list .= '<td><a href="./?'.$url_param.'order=rating">Rating</a></td>';
    $cat_list .= '</tr>';
    while ( $row = mysql_fetch_array($rows) ) {
        $cat_list .= '<tr>';
        $cat_list .= '<td><a><img class="thumbnail" width="57px" height="57px" src="'.$row['artworkUrl60'].'" alt="" /></a></td>';
        $cat_list .= '<td>';
        $cat_list .= '<p><a href="app-detail.php?id='.$row['trackId'].'">';
        if ($search_key != '') {
            $cat_list .= str_ireplace($search_key, "<b>$search_key</b>", $row['trackName']);
        } else {
            $cat_list .= $row['trackName'];
        }

        $cat_list .= '</a></p>';
        $cat_list .= '<p>Release Date: ' . $row['releaseDate'];
        $cat_list .= '</td>';
        $cat_list .= '<td><p>'.$row['primaryGenreName'];
        if ($app_tb == 'app') {
            $cat_list .= '<p><button onclick="add_to_papa(this, '.$row['trackId'].');">Add to PaPa</button>';
        }

        $cat_list .= '</td>';
        $cat_list .= '<td>'.$row['formattedPrice'].'</td>';
        $cat_list .= '<td>'.$row['averageUserRating'].'</td>'; 
        $cat_list .= '</tr>';
    } 

    $cat_list .= '</table>';
    return $cat_list;
}

//end file
