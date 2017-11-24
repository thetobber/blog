<?php
//declare(strict_types = 1);

require(__DIR__.'/autoload.php');

use Blog\Message\Stream;
use Blog\Message\Uri;
use Blog\Message\AbstractMessage;

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

abstract class AbstractController
{
    protected function render(string $filePath, $model): string
    {
        ob_start();
        include($filePath);
        return ob_get_clean();
        //$contents = ob_get_clean();
        //return gzencode($contents, 9);
    }
}

class Controller extends AbstractController
{
    public function index(): string
    {
        $model = [
            'test1' => 'Variable for testing.',
            'test2' => 'Hello world'
        ];

        return $this->render('test.php', $model);
    }
}

class Route
{
    protected $action;

    public function __construct(callable $action)
    {
        $this->action = $action;
    }

    public function callAction()
    {
        return call_user_func($this->action);
    }
}

$controller = new Controller();
$route = new Route([$controller, 'index']);

$stream = new Stream(fopen('php://temp', 'r+'));
$stream->write($route->callAction());

respond($stream);
