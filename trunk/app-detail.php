<?php    
require 'config.php'; 
require 'functions.php'; 

require('./templates/class_template.php');    
$path = './templates/';    
$tpl = & new Template($path);    

$app_tb = select_app_tb();

//app detail info
$trackId = (isset($_REQUEST['id']) AND $_REQUEST['id'] != '') ? $_REQUEST['id'] : ''; 

$sql  = "SELECT * FROM $app_tb Where trackId='$trackId'";
$rows = mysql_query($sql, $conn); 
$row = mysql_fetch_array($rows); 

//humit info
if ($app_tb == 'papa') {
    $sql   = "SELECT * FROM humit Where trackId='$trackId'";
    $rows  = mysql_query($sql, $conn); 
    $app_humit = mysql_fetch_array($rows); 
}

$app_humit['age'] = explode(',', $app_humit['age']);
foreach ($app_humit['age'] as $key => $age) {
    $app_humit['age'][$key] = trim($age);
} 

$humit_category = array('Musical', 'Bodily Kinesthetic', 'Logical Mathematical', 'Linguistic', 'Spatial', 'Interpersonal', 'Intrapersonal', 'Naturalist', 'Existential');
$humit_age      = array('0-1','1','2','3','4','5','5+','6','6+','7','7+','8+'); 

$sql   = "SELECT * FROM tags";
$rows_tags  = mysql_query($sql, $conn); 

$sql   = "SELECT * FROM tag_for_app WHERE trackId = $trackId";
$rows_app_tags = mysql_query($sql, $conn); 
$app_tags = array();
while ($app_tag = mysql_fetch_array($rows_app_tags)) {
    $app_tags[] = $app_tag['tag_id'];
} 

$tpl->set('title', 'User List');    
$tpl->set('app_tb', $app_tb);    
$tpl->set('row', $row);    
$tpl->set('humit_category', $humit_category);    
$tpl->set('app_humit', $app_humit);    
$tpl->set('humit_age', $humit_age);    
$tpl->set('app_tags', $app_tags);    
$tpl->set('rows_tags', $rows_tags);    

echo $tpl->fetch('app-detail.tpl.php');    

//end file
