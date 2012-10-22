<?php 
$batchDB['host'] = 'www.tomtalk.net';
$batchDB['user'] = 'root';
$batchDB['pwd']  = '';

$connP = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
mysql_select_db( 'apple_app', $connP ) OR die( 1 );
mysql_query( "set character set 'utf8'" );

$sql  = "SELECT * FROM app";
$rows = mysql_query( $sql , $connP );

while ( $row = mysql_fetch_array($rows) ) { 
    echo $row['app_name'];
} 
//end  file 
