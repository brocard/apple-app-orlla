<?php 
require 'config.php';

$conn = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
mysql_select_db('apple_app', $conn) OR die( 1 );
mysql_query( "set character set 'utf8'" );

$trackId = $_REQUEST['trackId'];

$sql  = "SELECT * FROM app";
$rows = mysql_query($sql, $conn);

while ( $row = mysql_fetch_array($rows) ) {
    echo $row['trackId'];
} 

//end  file 
