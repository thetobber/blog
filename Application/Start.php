<?php
declare(strict_types = 1);
// define('VIEW_DIR', __DIR__.'/View');

use Application\Message\ServerRequestFactory;
use Application\Message\Stream;
use Application\Message\Response;

use Application\Message\ServerRequestInterface;
use Application\Message\ResponseInterface;

use Application\Middleware\Dispatcher;
use Application\Middleware\RouteMiddleware;
use Application\Controller\TestController;

use Application\Routing\Route;

function respond(Stream $stream)
{
    $size = $stream->getSize();
    header('Content-Type: text/html;charset=UTF-8');
    //header('Content-Encoding: gzip');

    if ($size !== null) {
        header("Content-Length: $size");

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        while (!$stream->eof()) {
            echo $stream->read(Stream::CHUNK_SIZE);

            if (connection_status() != CONNECTION_NORMAL) {
                break;
            }
        }
    }
}

$request = ServerRequestFactory::getServerRequest();
$response = new Response();

$testController = new TestController($request, $response);

$routeMiddleware = new RouteMiddleware([
    new Route('@^(/|/index)$@', [$testController, 'index']),
    new Route('@^.*?$@', [$testController, 'notFound'])
]);

$dispatcher = new Dispatcher([$routeMiddleware]);

$contents = (string) $dispatcher($request, $response)->getBody();

respond($response->getBody());
