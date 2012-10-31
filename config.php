<?php 
session_start();

$batchDB['host'] = 'localhost';
$batchDB['user'] = 'root';
$batchDB['pwd']  = '890poi890poi';

$conn = mysql_connect( $batchDB['host'], $batchDB['user'], $batchDB['pwd'] ) OR die( 1 );
mysql_select_db('apple_app', $conn) OR die( 1 );
mysql_query( "set character set 'utf8'" );

//end file 
