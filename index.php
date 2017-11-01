<?php
require(__DIR__.'/autoload.php');

use Blog\Message\Stream;
use Blog\Message\Uri;
use Blog\Message\AbstractMessage;

function respond(Stream $stream)
{
    $size = $stream->getSize();
    header("Content-Type: text/html;charset=UTF-8");

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

$testModel = [
    'test1' => 'Variable for testing.',
    'test2' => 'Hello world'
];

function render(string $templateName, $model)
{
    ob_start();
    include($templateName);
    return ob_get_clean();
}

$stream = new Stream(fopen('php://temp', 'r+'));
$stream->write(
    render('test.php', $testModel)
);

respond($stream);
