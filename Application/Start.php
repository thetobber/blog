<?php
declare(strict_types = 1);
define('VIEWS_DIR', __DIR__.'/Views');

use Application\Libraries\Message\Stream;
use Application\Libraries\Routing\Route;
use Application\Controllers\Controller;

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

$controller = new Controller();
$route = new Route([$controller, 'index']);

$stream = new Stream(fopen('php://temp', 'r+'));
$stream->write($route->callAction());

respond($stream);
