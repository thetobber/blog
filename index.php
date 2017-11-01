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
            print $stream->read(Stream::CHUNK_SIZE);

            if (connection_status() != CONNECTION_NORMAL) {
                break;
            }
        }
    }
}

$stream = new Stream(fopen('php://temp', 'r+'));

$stream->write('test');
//respond($stream);
?>

<pre>
    <?php print_r($stream); ?>
</pre>