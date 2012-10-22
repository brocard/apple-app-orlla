<?php 
require 'config.php';

$connP = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
mysql_select_db( 'apple_app', $connP ) OR die( 1 );
mysql_query( "set character set 'utf8'" );

$trackId = $_REQUEST['trackId'];

$sql  = "INSERT INTO app (trackId) VALUES ($trackId)";

if (mysql_query( $sql , $connP )) {
    echo 'ok'; 
}  else {
    echo 'DB operating failuer!';
}

//end  file 
