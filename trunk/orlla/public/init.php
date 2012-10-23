<?php
/**
 $Id: init.php 960 2008-01-18 09:32:18Z legend $
*/


error_reporting(E_ALL | E_NOTICE);
date_default_timezone_set('PRC');

define('TIMENOW', time());

//session_start();

// timer
$timer = new Timer();

require_once SOURCES_PATH . "functions.php";

// load classes
require_once SOURCES_PATH . "class_site.php";
require_once SOURCES_PATH . "class_template.php";
require_once SOURCES_PATH . "class_db.php";
require_once SOURCES_PATH . "class_mysql.php";
require_once SOURCES_PATH . "class_session.php";

// site
$site = & new Site($INFO);

$db = new MySql($site->vars['db']);

$site->db = & $db;

$db->query("SET NAMES UTF8");

// template
$tpl = & new Template($site);
$site->tpl = &$tpl;

// session
//$site->session = & new Session($site);
//$site->fetch_session();

// messages
require_once SOURCES_PATH . "global_messages.php";
$site->messages = &$_MSG;

$site->timer = &$timer;

/*
if ($db->get_row("SELECT * FROM cron WHERE NextTime <= " . TIMENOW . " AND Active = 1 LIMIT 1"))
{
    $site->cron = true;
}
*/


// class timer
class Timer
{
    var $starttime;
    var $time;
    var $step;

    function timer()
    {
        $this->starttime = $this->get_microtime();
        $this->step = $this->starttime;
    }

    function get_microtime()
    {
       list($usec, $sec) = explode(" ", microtime());
       return ((float)$usec + (float)$sec);
    } 

    function end()
    {
        $endtime = $this->get_microtime();
        
        $this->time = $endtime - $this->starttime;
    }

    function step()
    {
        $current = $this->get_microtime();
        $time = $current - $this->step;
        $this->step = $current;

        return $time;
    }
}
?>
