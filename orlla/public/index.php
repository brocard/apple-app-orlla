<?php
define('IN_SITE', true);
require_once "./config.php";
require_once "./init.php";
require_once "./function.php";

$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';

$sql = 'SELECT page_title, page_counter FROM page WHERE page_len!=0 ORDER BY page_latest DESC';
//$wikiPage = $site->db->get_all($sql);
//$site->tpl->set('wikiPage', $wikiPage); 

if ($order == '') {
    $sql = 'SELECT page_title, page_counter FROM page WHERE page_len!=0 ORDER BY page_counter DESC';
    $wikiPage = $site->db->get_all($sql);
    $site->tpl->set('page_counter', $wikiPage); 
}

if ($order == 'new') {
    $sql = 'SELECT page_title, page_counter FROM page WHERE page_is_new=1 AND page_len!=0 ORDER BY page_latest DESC';
    $wikiPage = $site->db->get_all($sql);
    $site->tpl->set('page_counter', $wikiPage); 
}

//$tpl->import_style('site.css');
$tpl->display("index.tpl.php");
?>
