<?php
declare(strict_types = 1);
namespace Application\Message;

class Request extends AbstractMessage implements RequestInterface
{
    const METHODS = [
        'GET' => true,
        'POST' => true
    ];

    /**
     * @var string
     */
    protected $requestTarget;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var UriInterface
     */
    protected $uri;

    public function __construct(
        string $method,
        string $protocolVersion,
        UriInterface $uri,
        array $headers,
        StreamInterface $body
    ) {
        $this->method = $method;
        $this->protocolVersion = $protocolVersion;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function __clone()
    {
        $this->uri = clone $this->uri;
        $this->body = clone $this->body;
    }

    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $query = $this->uri
            ->getQuery();

        $this->requestTarget = $this->uri
            ->getPath().(empty($query) ?  '' : '?'.$query);

        return $this->requestTarget;
    }

    public function withRequestTarget($requestTarget)
    {
        $clone = clone $this;
        $clone->requestTarget = trim($requestTarget);

        return $clone;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method)
    {
        if (!isset(self::METHODS[$method])) {
            throw new InvalidArgumentException();
        }

        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false)
    {
        $clone = clone $this;
        $clone->uri = $uri;

        if (!$preserveHost) {
            $port = $uri
                ->getPort();

            $host = $uri
                ->getHost().($port === null ? '' : ':'.$port);

            if (!empty($host)) {
                $clone->header = ['host' => [$host]] + $clone->headers;
                $clone->headerLines = ['Host' => [$host]]+ $clone->headersLines;
            }
        }

        return $clone;
    }
}
