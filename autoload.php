<?php
/**
* @link http://php.net/manual/en/function.spl-autoload-register.php
*/

$classMap = [
    'Application\\Libraries\\Message\\Interfaces\\MessageInterface'         => '/Application/Libraries/Message/Interfaces/MessageInterfacephp',
    'Application\\Libraries\\Message\\Interfaces\\RequestInterface'         => '/Application/Libraries/Message/Interfaces/RequestInterface.php',
    'Application\\Libraries\\Message\\Interfaces\\ResponseInterface'        => '/Application/Libraries/Message/Interfaces/ResponseInterface.php',
    'Application\\Libraries\\Message\\Interfaces\\ServerRequestInterface'   => '/Application/Libraries/Message/Interfaces/ServerRequestInterface.php',
    'Application\\Libraries\\Message\\Interfaces\\StreamInterface'          => '/Application/Libraries/Message/Interfaces/StreamInterface.php',
    'Application\\Libraries\\Message\\Interfaces\\UploadedFileInterface'    => '/Application/Libraries/Message/Interfaces/UploadedFileInterface.php',
    'Application\\Libraries\\Message\\Interfaces\\UriInterface'             => '/Application/Libraries/Message/Interfaces/UriInterface.php',
    'Application\\Libraries\\Message\\Stream'                               => '/Application/Libraries/Message/Stream.php',
    'Application\\Libraries\\Message\\Uri'                                  => '/Application/Libraries/Message/Uri.php',
    'Application\\Libraries\\Message\\AbstractMessage'                      => '/Application/Libraries/Message/AbstractMessage.php',

    'Application\\Libraries\\Routing\\Route'                                => '/Application/Libraries/Routing/Route.php',
    'Application\\Libraries\\Structure\\AbstractController'                 => '/Application/Libraries/Structure/AbstractController.php',
    'Application\\Libraries\\Structure\\AbstractRepository'                 => '/Application/Libraries/Structure/AbstractRepository.php',

    'Application\\Controllers\\Controller'                                  => '/Application/Controllers/Controller.php'
];

spl_autoload_register(function ($className) use ($classMap) {
    if (isset($classMap[$className])) {
        require(__DIR__.$classMap[$className]);
    }
}, true, true);
