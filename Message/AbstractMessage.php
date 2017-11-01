<?php
namespace Blog\Message;

use InvalidArgumentException;
use Blog\Message\Stream;

abstract class AbstractMessage
{
    /**
     * Accepted versions of the HTTP protocol for this implementation.
     *
     * @var array
     */
    const PROTOCOL_VERSIONS = array(
        '1.0' => true,
        '1.1' => true
    );

    /**
     * HTTP version of the message.
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * All HTTP headers of the message with lowercased keys.
     *
     * @var array
     */
    protected $headers; //Contains headers as lowercase

    /**
     * All HTTP headers of the message with their original casing. Notice that
     * headers fetched from the $_SERVER super global will be formatted
     * accordingly to the HTTP standard. This is because headers from PHP is
     * formatted to comply with the CGI standards.
     *
     * @var array
     */
    protected $headerLines; //Headers with their original case

    /**
     * Body of the message as a stream.
     *
     * @var StreamInterface
     */
    protected $body;

    /**
     * Get the protocol version.
     *
     * @return string Returns the protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Clone the instance with a new protocol version.
     *
     * @param string $version The protocol version to use e.g. "1.0" or "1.1".
     * @return self Returns a cloned instance with a new protocol version.
     * @throws InvalidArgumentException on invalid protocol version.
     */
    public function withProtocolVersion(string $version): self
    {
        if (!isset(self::PROTOCOL_VERSIONS[$version])) {
            throw new InvalidArgumentException();
        }

        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    /**
     * Retrieves all HTTP headers for the message.
     *
     * @return array Returns an associative array of the HTTP headers container
     *   container in the message.
     */
    public function getHeaders(): array
    {
        return $this->headerLines;
    }

    /**
     * Checks whether the given message header exists. The check is peformed 
     * case-insensitive.
     *
     * @param string $name Name of the header field.
     * @return bool Returns true if the header exists and false if it does not.
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * Retrieves the values from a single message header as an array.
     * 
     * @param string $name Name of the header field.
     * @return array Returns an array of the message header values.
     */
    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    /**
     * 
     * 
     * @param string $name
     * @return string
     */
    public function getHeaderLine(string $name): string
    {
        $header = $this->getHeader($name);
        return empty($header) ? '' : implode(', ', $header);
    }

    /**
     * Clone and returns the instance with the specified and value. If the 
     * header does not exist in the message it will be added. If the header 
     * exists it will be replaced.
     * 
     * @link https://regex101.com/r/VqHPJD/1 /^[a-z]+(?>-[a-z]+)*$/i
     * @param string $name Name of the message header.
     * @param string[]|string $value Value(s) to add with the header.
     * @return self Returns a cloned instance with the header.
     */
    public function withHeader(string $name, $value): self
    {
        /*
        Regular expression for matching HTTP headers. For example: Content-Type, 
        Accept or X-UA-Compatible.
        */
        if (preg_match('/^[a-z]+(?>-[a-z]+)*$/i', $name) === 0) {
            throw new InvalidArgumentException();
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
     * Clone and returns the instance with the specified and value. If the 
     * header does not exist it will be added. If the header does exist the 
     * will be added to the existing header insead.
     * 
     * @param string $name Name of the message header.
     * @param string[]|string $value Value(s) to add with the header.
     * @return self Returns a cloned instance with the header or new value.
     */
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

    /**
     * Clone and returns the instance without the specified header. The header 
     * name is checked case-insensitive.
     * 
     * @param string $name Name of the header to remove.
     * @return self Returns a cloned instance without the header.
     */
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

    /**
     * @return Stream
     */
    public function getBody(): Stream
    {
        return $this->body;
    }

    /**
     * @param Stream $body
     * @return self
     */
    public function withBody(Stream $body): self
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }
}
