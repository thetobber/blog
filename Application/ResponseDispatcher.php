<?php
declare(strict_types = 1);
namespace Application;

use Application\Message\Stream;
use Application\Message\ResponseInterface as Response;

class ResponseDispatcher
{
    protected static function setHttpHeader(Response $response): void
    {
        $statusCode = $response->getStatusCode();

        $header = sprintf('HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $statusCode,
            $response->getReasonPhrase()
        );

        header($header, true, $statusCode);
        // header($headers, true, $statusCode);
    }

    protected static function setHeaders(Response $response): void
    {
        $headers = $response->getHeaders();

        if (!empty($headers)) {
            foreach ($headers as $key => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $key, $value), false);
                }
            }
        }
    }

    public static function sendResponse(Response $response): void
    {
        if (!headers_sent()) {
            self::setHttpHeader($response);
            self::setHeaders($response);
        }

        $responseSize = $response->getBody()->getSize();

        if ($responseSize !== null) {
            $body = $response->getBody();

            if ($body->isSeekable()) {
                $body->rewind();
            }

            while (!$body->eof()) {
                echo $body->read(Stream::CHUNK_SIZE);

                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }
        }
    }
}
