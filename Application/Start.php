<?php
declare(strict_types = 1);

use Application\Message\Stream;
use Application\Message\ServerRequestInterface;
use Application\Message\ServerRequestFactory;
use Application\Message\ResponseInterface;
use Application\Message\Response;

use Application\Routing\Route;
use Application\Middleware\Dispatcher;
use Application\Middleware\RouteMiddleware;
use Application\Controller\TestController;
use Application\Controller\AuthController;
use Application\ResponseDispatcher;

$request = ServerRequestFactory::getServerRequest();
$response = new Response();

$testCtrl = new TestController($request, $response);
$authCtrl = new AuthController($request, $response);

$dispatcher = new Dispatcher([
    new RouteMiddleware([
        new Route('@^/register$@i', 'get', [$authCtrl, 'getRegister']),
        new Route('@^/register$@i', 'post', [$authCtrl, 'postRegister']),
        new Route('@^.*?$@', 'all', [$testCtrl, 'notFound'])
    ])
]);

$response = $dispatcher($request, $response);

ResponseDispatcher::sendResponse($response);
