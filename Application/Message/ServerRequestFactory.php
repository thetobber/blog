<?php
namespace Application\Message;

use InvalidArgumentException;
use Application\Message\ServerRequestInterface;
use Application\Message\ServerRequest;
use Application\Message\UriInterface;
use Application\Message\Uri;
use Application\Message\UploadedFileInterface;
use Application\Message\UploadedFile;
use Application\Message\StreamInterface;
use Application\Message\Stream;

class ServerRequestFactory
{
    /**
    * @var array
    */
    const WEIRD_HEADERS = [
        'CONTENT_TYPE' => 'Content-Type',
        'CONTENT_LENGTH' => 'Content-Length'
    ];

    private function __construct() {}

    public static function getServerRequest(
        array $attributes = []
    ): ServerRequestInterface {
        $requestUri = self::getUri();
        $requestQuery = [];

        parse_str(
            $requestUri->getQuery(),
            $requestQuery
        );

        return new ServerRequest(
            $_SERVER['REQUEST_METHOD'],
            self::getProtocolVersion(),
            $requestUri,
            self::getHeaders($_SERVER),
            self::getBody(),
            self::getParams(),
            $_COOKIE,
            $requestQuery,
            self::getUploadedFiles(),
            $attributes
        );
    }

    public static function getParams(): array
    {
        return array_filter(
            $_SERVER,
            function ($key) {
                return strpos($key, 'HTTP_') === false &&
                    !isset(self::WEIRD_HEADERS[$key]);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    public static function getProtocolVersion(): string
    {
        return str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']);
    }

    public static function getUploadedFiles(): array
    {
        $files = [];

        foreach ($_FILES as $file) {
            if (empty($file['tmp_name'])) {
                continue;
            }

            $files[] = new UploadedFile(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                $file['name'],
                $file['type']
            );
        }

        return $files;
    }

    public static function getUri(): UriInterface
    {
        return new Uri(
            empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https',
            !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'],
            isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : null,
            isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '',
            isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '',
            isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/',
            isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''
        );
    }

    public static function getBody(): StreamInterface
    {
        $temp = fopen('php://temp', 'w+');

        stream_copy_to_stream(
            fopen('php://input', 'r'),
            $temp
        );

        rewind($temp);

        return new Stream($temp);
    }

    public static function getHeaders(array $server): array
    {
        if (is_callable('apache_request_headers')) {
            return apache_request_headers();
        }

        $headers = [];

        foreach ($server as $key => $value) {
            if (isset(self::WEIRD_HEADERS[$key])) {
                $headers[self::WEIRD_HEADERS[$key]] = $value;
                continue;
            }

            if (($normalizedKey = self::normalizeKey($key, 'HTTP_')) !== null) {
                $headers[$normalizedKey] = $value;
            }
        }

        return $headers;
    }

    public static function normalizeKey(string $key, string $prefix = ''): ?string
    {
        if ($prefix !== '' && strpos($key, $prefix) !== 0) {
            return null;
        }

        return str_replace(
            ' ',
            '-',
            ucwords(
                str_replace('_', ' ',
                    substr(
                        strtolower($key),
                        strlen(strtolower($prefix))
                    )
                )
            )
        );
    }
}
