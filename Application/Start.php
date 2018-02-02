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
use Application\Controller\DefaultController;
use Application\Controller\AuthController;
use Application\ResponseDispatcher;

$request = ServerRequestFactory::getServerRequest();
$response = new Response();

$defaultCtrl = new DefaultController();
$authCtrl = new AuthController();

$dispatcher = new Dispatcher([
    new RouteMiddleware([
        new Route('@^/register$@i', 'get', [$authCtrl, 'getRegister']),
        new Route('@^/register$@i', 'post', [$authCtrl, 'postRegister']),

        new Route('@^/signin$@i', 'get', [$authCtrl, 'getSignIn']),
        new Route('@^/signin$@i', 'post', [$authCtrl, 'postSignIn']),

        new Route('@^/signout$@i', 'post', [$authCtrl, 'postSignOut']),
        new Route('@^/profile$@i', 'get', [$authCtrl, 'getProfile']),

        // new Route('@^/user/(?<username>.{0,191})$@iu', 'get', [$authCtrl, 'getUser']),
        new Route('@^(|/)$@', 'get', [$defaultCtrl, 'index']),
        new Route('@^.*?$@', 'all', [$defaultCtrl, 'notFound'])
    ])
]);

$response = $dispatcher($request, $response);

ResponseDispatcher::sendResponse($response);
