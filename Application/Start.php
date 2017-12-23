<?php
declare(strict_types = 1);
define('VIEW_DIR', __DIR__.'/View');

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

require 'Test.php';
