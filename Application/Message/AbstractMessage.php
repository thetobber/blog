<?php
declare(strict_types = 1);
namespace Application\Message;

use InvalidArgumentException;
use Application\Message\MessageInterface;
use Application\Message\StreamInterface;

abstract class AbstractMessage implements MessageInterface
{
    /**
     * @var array
     */
    const PROTOCOL_VERSIONS = [
        '1.0' => true,
        '1.1' => true,
        '2.0' => true
    ];

    /**
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * @var array
     */
    protected $headers; //Contains headers as lowercase

    /**
     * @var array
     */
    protected $headerLines; //Headers with their original case

    /**
     * @var StreamInterface
     */
    protected $body;

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): self
    {
        if (!isset(self::PROTOCOL_VERSIONS[$version])) {
            throw new InvalidArgumentException();
        }

        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    public function getHeaders(): array
    {
        return $this->headerLines;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader(string $name): array
    {
        $headerValue = $this->headers[strtolower($name)] ?? [];

        if (is_array($headerValue))  {
            return $headerValue;
        }

        return [$headerValue];
    }

    public function getHeaderLine(string $name): string
    {
        $header = $this->getHeader($name);
        return empty($header) ? '' : implode(', ', $header);
    }

    public function withHeader(string $name, $value): self
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

    public function withAddedHeader(string $name, $value): self
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        $clone = clone $this;
        $clone->headers[strtolower($name)][] = $value;
        $clone->headerLines[$name][] = $value;

        return $clone;
    }

    public function withoutHeader(string $name): self
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

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): self
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }
}
