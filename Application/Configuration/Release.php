<?php
ini_set('display_errors', 0);

session_start([
    'name' => 'vf56p3x0',
    'use_strict_mode' => 1,
    'cookie_httponly' => 1,
    'use_only_cookies' => 1,
    'cookie_secure' => 1,
    'referer_check' => 1,
    'gc_maxlifetime' => 3600,
    'cookie_lifetime' => 3600,
    'cookie_domain' => '.tobymw.dk'
]);

define('ROOT_DIR', dirname(__DIR__));
define('VIEW_DIR', ROOT_DIR.'/View');
define('STATIC_DIR', dirname(ROOT_DIR).'/Static');

define('CONNECTION_STRING', 'mysql:host=localhost;dbname=spot;charset=utf8');
define('DB_USER', 'spot');
define('DB_PASS', 'JW`S$(\&3JG?Ba!A');
