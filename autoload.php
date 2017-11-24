<?php
/**
* @link http://php.net/manual/en/function.spl-autoload-register.php
*/

$classMap = [
    'Blog\\Message\\Stream' => '/Message/Stream.php',
    'Blog\\Message\\Uri' => '/Message/Uri.php',
    'Blog\\Message\\AbstractMessage' => '/Message/AbstractMessage.php'
];

spl_autoload_register(function ($className) use ($classMap) {
    if (isset($classMap[$className])) {
        require(__DIR__.$classMap[$className]);
    }
}, true, true);
