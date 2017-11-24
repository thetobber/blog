<?php
namespace Blog\Message;

use InvalidArgumentException;
use Blog\Message\Stream;

use Blog\Message\Interfaces\MessageInterface;
use Blog\Message\Interfaces\StreamInterface;

abstract class AbstractMessage implements MessageInterface
{
    /**
     * Accepted versions of the HTTP protocol for this implementation.
     *
     * @var array
     */
    const PROTOCOL_VERSIONS = [
        '1.0' => true,
        '1.1' => true,
        '2.0' => true
    ];

    /**
     * HTTP version of the message.
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * Contains the HTTP headers with their names as lower case.
     *
     * @var string[][]
     */
    protected $headers; //Contains headers as lowercase

    /**
     * All HTTP headers of the message with their original case. Notice that
     * headers fetched from the $_SERVER super global will not be formatted
     * accordingly with the HTTP standard. This is because headers from PHP is
     * formatted to comply with the CGI standards.
     *
     * @var string[][]
     */
    protected $headerLines; //Headers with their original case

    /**
     * Body of the message as a stream.
     *
     * @var StreamInterface
     */
    protected $body;

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion(string $version): MessageInterface
    {
        if (!isset(self::PROTOCOL_VERSIONS[$version])) {
            throw new InvalidArgumentException();
        }

        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headerLines;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * @inheritDoc
     */
    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine(string $name): string
    {
        $header = $this->getHeader($name);
        return empty($header) ? '' : implode(', ', $header);
    }

    /**
     * @inheritDoc
     */
    public function withHeader(string $name, $value): MessageInterface
    {
        /**
         * Regular expression for matching HTTP headers. For example:
         * Content-Type, Accept or X-UA-Compatible.
         *
         * @link https://regex101.com/r/VqHPJD/1
         */
        if (preg_match('/^[a-z]+(?>-[a-z]+)*$/i', $name) === 0) {
            throw new InvalidArgumentException('Argument $name must be a valid HTTP header');
        }

        $clone = clone $this;
        $header = strtolower($name);

        if (is_string($value)) {
            $clone->headers[$header] = array($value);
        } elseif (is_array($value)) {
            $clone->headers[$header] = $value;
        } else {
            throw new InvalidArgumentException();
        }

        foreach (array_keys($clone->headerLines) as $key) {
            if (strtolower($key) === $header) {
                unset($clone->headerLines[$key]);
            }
        }

        $clone->headerLines[$name] = $clone->headers[$header];

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader(string $name, $value): MessageInterface
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        $clone = clone $this;
        $clone->headers[strtolower($name)][] = $value;
        $clone->headerLines[$name][] = $value;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader(string $name): MessageInterface
    {
        if (!$this->hasHeader($name)) {
            return $this;
        }

        $clone = clone $this;
        $header = strtolower($name);

        unset($clone->headers[$header]);

        foreach (array_keys($clone->headerLines) as $key) {
            if (strtolower($key) === $header) {
                unset($clone->headerLines[$key]);
            }
        }

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody(Stream $body): MessageInterface
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }
}
