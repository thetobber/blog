<?php
declare(strict_types = 1);

use Application\Message\ServerRequestFactory;
use Application\Message\Response;

use Application\Routing\Route;
use Application\ResponseDispatcher;

use Application\Middleware\Dispatcher;
use Application\Middleware\RouteMiddleware;

use Application\Controller\DefaultController;
use Application\Controller\AuthController;
use Application\Controller\PostController;

$request = ServerRequestFactory::getServerRequest();
$response = new Response();

$defaultCtrl = new DefaultController();
$authCtrl = new AuthController();
$postCtrl = new PostController();

$dispatcher = new Dispatcher([
    new RouteMiddleware([
        new Route('@^(|/)$@', 'get', [$defaultCtrl, 'index']),
        new Route('@^/register$@i', 'get', [$authCtrl, 'getRegister']),
        new Route('@^/register$@i', 'post', [$authCtrl, 'postRegister']),

        new Route('@^/signin$@i', 'get', [$authCtrl, 'getSignIn']),
        new Route('@^/signin$@i', 'post', [$authCtrl, 'postSignIn']),
        new Route('@^/signout$@i', 'post', [$authCtrl, 'postSignOut']),

        new Route('@^/update-password$@i', 'post', [$authCtrl, 'postUpdatePassword']),
        new Route('@^/update-email$@i', 'post', [$authCtrl, 'postUpdateEmail']),
        new Route('@^/(profile|update-password|update-email)$@i', 'get', [$authCtrl, 'getProfile']),

        new Route('@^/summary$@i', 'get', [$postCtrl, 'getByAuthor']),
        new Route('@^/person/(?<username>.{1,191})$@i', 'get', [$postCtrl, 'getByOwner']),
        new Route('@^/person/(?<username>.{1,191})$@i', 'post', [$postCtrl, 'postToOwner']),

        new Route('@^.*?$@', 'all', [$defaultCtrl, 'notFound'])
    ])
]);

$response = $dispatcher($request, $response);

ResponseDispatcher::sendResponse($response);
