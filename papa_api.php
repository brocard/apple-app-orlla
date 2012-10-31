<?php
require 'config.php'; 
require 'functions.php'; 

$conn = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
mysql_select_db('apple_app', $conn) OR die( 1 );
mysql_query( "set character set 'utf8'" );

$op = $_REQUEST['op'];

if ( $op == 'add_to_papa') {
    $trackId = $_REQUEST['trackId'];
    add_to_papa($trackId, $conn);
    echo 'ok';
} else if ($op == 'add_search_to_papa') {
    $search = $_REQUEST['search'];
    $cat = $_REQUEST['cat'];
    if ($cat != '') {
        $cat = " primaryGenreName = '$cat' AND ";
    }

    $sql = "SELECT trackId, trackName FROM app WHERE $cat MATCH (trackName) AGAINST ('$search')";
    $rows = mysql_query($sql, $conn);
    while ($row = mysql_fetch_array($rows)) {
        echo '<p>'.$row['trackName'];
        add_to_papa($row['trackId'], $conn);
    } 
    echo '<p>finish!';
} else if ($op == 'humit_info_save') {
    $trackId = $_REQUEST['trackId'];
    $humit   = $_REQUEST['humit'];
    $gender  = $_REQUEST['gender'];
    $age     = implode(',', $_REQUEST['age']);
    $tags    = $_REQUEST['tags'];

    $sql = "UPDATE humit SET humit='$humit', gender='$gender', age='$age' "
         . "WHERE trackId=$trackId";
    mysql_query($sql, $conn); 

    //删除已有tags
    $sql = "DELETE FROM tag_for_app WHERE trackId = $trackId";
    mysql_query($sql, $conn); 

    //添加新tags
    $sql = "INSERT INTO tag_for_app (trackId, tag_id) VALUES ";
    $values = array();
    foreach ($tags as $tag) {
        $values[] = "($trackId, $tag)";
    }
    $sql .= implode(',', $values);
    mysql_query($sql, $conn); 

    echo 'ok'; 
} else if ($op == 'add_tag') {
    $tag = $_REQUEST['tag'];
    $sql = "INSERT INTO tags (tag) values('$tag')";
    mysql_query($sql, $conn); 
    echo 'ok'; 
}

mysql_close($conn);

function add_to_papa ($trackId, $conn) {
    $sql = "INSERT INTO humit (trackId, editor_id) values ($trackId, 100)";
    mysql_query($sql, $conn); 

    $sql = "INSERT INTO papa (SELECT * FROM app WHERE trackId=$trackId)";
    mysql_query($sql, $conn); 

    $sql = "DELETE from app WHERE trackId=$trackId";
    mysql_query($sql, $conn); 
}

//end  file 
