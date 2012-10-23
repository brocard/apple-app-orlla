<?php

// disallow run this file directly
if (!defined('IN_SITE'))
{
    print "<h1>Incorrect access</h1>";
    exit();
}

// absolute root path of site
define('ROOT_PATH', realpath(dirname(__FILE__) . "/../") . "/");

// classes, functions dir
define('SOURCES_PATH'  , ROOT_PATH . 'sources/');

$INFO = array (
        'site' => 
        array (
            'fields' => 
            array (
                0 => 'domain',
                1 => 'data_path',
                2 => 'data_url',
                ),
            'domain' => 'www.tomtalk.net',
            'sources_path' => 'E:\\G3_Lite/sources/',
            'cron_path' => 'E:\\G3_Lite/public/',
            'cron_url' => '/cron/cron.php',
            'global_scripts' => 
            array (
                0 => 'common.js',
                ),
            'global_styles' => 
            array (
                0 => 'info.css',
                ),
            'version' => '1.0',
            'data_path' => 'e:/g3/files/data/',
            'data_url' => 'http://static.doyouhike.net/files/',
            ),
            'session' => 
            array (
                    'fields' => 
                    array (
                        ),
                    'lifetime' => 1200,
                  ),
            'cookie' => 
            array (
                    'fields' => 
                    array (
                        0 => 'prefix',
                        1 => 'domain',
                        2 => 'expire',
                        ),
                    'prefix' => 'dyh_',
                    'domain' => 'www.g3.com',
                    'expire' => '2628000',
                  ),
            'db' => 
            array (
                    'fields' => 
                    array (
                        0 => 'host',
                        1 => 'db',
                        2 => 'username',
                        3 => 'password',
                        ),
                    'host' => 'www.tomtalk.net',
                    'db' => 'tom_wiki',
                    'username' => 'root',
                    'password' => '',
                  ),
            'template' => 
            array (
                    'fields' => 
                    array (
                        0 => 'templates_path',
                        1 => 'static_path',
                        ),
                    'templates_path' => 'D:\php\htdocs\itune\orlla\templates',
                    'static_path' => '/itune/orlla/static/',
                    'errors_stand_alone' => 'errors_stand_alone.tpl.php',
                    'flash' => 'flash.tpl.php',
                  ),
            'mail' => 
            array (
                    'fields' => 
                    array (
                        0 => 'smtp_host',
                        1 => 'smtp_port',
                        2 => 'smtp_user',
                        3 => 'smtp_pass',
                        4 => 'sender_name',
                        ),
                    'smtp_host' => 'mail.doyouhike.net',
                    'smtp_port' => '25',
                    'smtp_user' => 'reg@doyouhike.net',
                    'smtp_pass' => 'regadmin',
                    'sender_name' => 'doyouhike',
                  ),
            );

//end file
