<?php
/**
* @link http://php.net/manual/en/function.spl-autoload-register.php
*/

$prefix = 'Application';

$classMap = [
    // Message interfaces
    "$prefix\\Message\\StreamInterface"          => "$prefix/Message/StreamInterface.php",
    "$prefix\\Message\\UriInterface"             => "$prefix/Message/UriInterface.php",
    "$prefix\\Message\\MessageInterface"         => "$prefix/Message/MessageInterface.php",
    "$prefix\\Message\\RequestInterface"         => "$prefix/Message/RequestInterface.php",
    "$prefix\\Message\\ServerRequestInterface"   => "$prefix/Message/ServerRequestInterface.php",
    "$prefix\\Message\\UploadedFileInterface"    => "$prefix/Message/UploadedFileInterface.php",
    "$prefix\\Message\\ResponseInterface"        => "$prefix/Message/ResponseInterface.php",

    // Message classes
    "$prefix\\Message\\Stream"                   => "$prefix/Message/Stream.php",
    "$prefix\\Message\\Uri"                      => "$prefix/Message/Uri.php",
    "$prefix\\Message\\Message"                  => "$prefix/Message/Message.php",
    "$prefix\\Message\\Request"                  => "$prefix/Message/Request.php",
    "$prefix\\Message\\ServerRequest"            => "$prefix/Message/ServerRequest.php",
    "$prefix\\Message\\UploadedFile"             => "$prefix/Message/UploadedFile.php",
    "$prefix\\Message\\Response"                 => "$prefix/Message/Response.php",

    // Middleware
    "$prefix\\Middleware\\MiddlewareInterface"   => "$prefix/Middleware/MiddlewareInterface.php",

    // Routing
    "$prefix\\Routing\\Route"                    => "$prefix/Routing/Route.php",

    // Controller
    "$prefix\\Controller\\AbstractController"    => "$prefix/Controller/AbstractController.php",
    "$prefix\\Controller\\TestController"        => "$prefix/Controller/TestController.php"
];

spl_autoload_register(function ($className) use ($classMap) {
    if (isset($classMap[$className])) {
        require(__DIR__.'/'.$classMap[$className]);
    }
}, true, true);
