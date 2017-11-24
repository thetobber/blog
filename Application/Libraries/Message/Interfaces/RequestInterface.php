<?php
namespace Application\Libraries\Message\Interfaces;

use Application\Libraries\Message\Interfaces\MessageInterface;
use Application\Libraries\Message\Interfaces\UriInterface;

/**
 * @link https://github.com/php-fig/http-message/blob/master/src/RequestInterface.php
 */
interface RequestInterface extends MessageInterface
{
    public function getRequestTarget(): string;
    public function withRequestTarget($requestTarget);
    public function getMethod(): string;
    public function withMethod(string $method);
    public function getUri(): UriInterface;
    public function withUri(UriInterface $uri, bool $preserveHost = false);
}