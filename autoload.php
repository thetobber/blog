<?php
/**
* @link http://php.net/manual/en/function.spl-autoload-register.php
*/

$prefix = 'Application';

$classMap = [
    "$prefix\\Message\\MessageInterface"         => "$prefix/Message/Interfaces/MessageInterface.php",
    "$prefix\\Message\\RequestInterface"         => "$prefix/Message/Interfaces/RequestInterface.php",
    "$prefix\\Message\\ResponseInterface"        => "$prefix/Message/Interfaces/ResponseInterface.php",
    "$prefix\\Message\\ServerRequestInterface"   => "$prefix/Message/Interfaces/ServerRequestInterface.php",
    "$prefix\\Message\\StreamInterface"          => "$prefix/Message/Interfaces/StreamInterface.php",
    "$prefix\\Message\\UploadedFileInterface"    => "$prefix/Message/Interfaces/UploadedFileInterface.php",
    "$prefix\\Message\\UriInterface"             => "$prefix/Message/Interfaces/UriInterface.php",
    "$prefix\\Message\\Stream"                   => "$prefix/Message/Stream.php",
    "$prefix\\Message\\Uri"                      => "$prefix/Message/Uri.php",
    "$prefix\\Message\\AbstractMessage"          => "$prefix/Message/AbstractMessage.php",

    "$prefix\\Routing\\Route"                    => "$prefix/Routing/Route.php",

    "$prefix\\Controller\\AbstractController"    => "$prefix/Controller/AbstractController.php",
    "$prefix\\Controller\\TestController"        => "$prefix/Controller/TestController.php"
];

spl_autoload_register(function ($className) use ($classMap) {
    if (isset($classMap[$className])) {
        require(__DIR__.'/'.$classMap[$className]);
    }
}, true, true);
