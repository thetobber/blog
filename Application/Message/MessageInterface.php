<?php
declare(strict_types = 1);
namespace Application\Message;

use Application\Message\StreamInterface;

/**
 * HTTP messages consist of requests from a client to a server and responses
 * from a server to a client. This interface defines implementations common for
 * each.
 *
 * Messages are considered immutable. Methods that might change the state of the
 * current instance MUST be implemented to retain immutability of the message
 * and therefore return an instance with the changed state.
 *
 * @link http://www.ietf.org/rfc/rfc7230.txt
 * @link http://www.ietf.org/rfc/rfc7231.txt
 */
interface MessageInterface
{
    /**
     * Retrieves the HTTP protocol version.
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion(): string;

    /**
     * Return an instance with the specified HTTP protocol version. The argument
     * MUST only contain the protocol version number.
     *
     * This method MUST be implemented to retain immutability of the message and
     * MUST return an instance with the specified protocol version.
     *
     * @param string $version HTTP protocol version.
     * @return static
     */
    public function withProtocolVersion(string $version);

    /**
     * Retrieves all the HTTP headers of the message. The keys represents the
     * header name, whereas the value is an array of strings.
     *
     * This method MUST preserve the exact case in which the header names were
     * originally specified.
     *
     * @return string[][] An associative array of the message's headers. Each
     *     key MUST be a header name and the associated value MUST be an array
     *     of strings.
     */
    public function getHeaders(): array;

    /**
     * Checks if the specified header exists. This check MUST be performed in a
     * case-insensitive manner.
     *
     * @param string $name Header name.
     * @return bool True if the header name exists or false if the header name
     *     could not be found in the message.
     */
    public function hasHeader(string $name): bool;

    /**
     * Retrieves a sinlge HTTP header from the message. This retrieval must be
     * performed in a case-insensitive manner.
     *
     * This method returns all header values of the specified header name. An
     * empty array MUST be returned if the given header is not found in the
     * message.
     *
     * @param string $name Header name.
     * @return string[] An array of the header values or an empty array if the
     *     specified header is not found.
     */
    public function getHeader(string $name): array;

    /**
     * Retrieves all the header values as a comma-separated string for the
     * specified header. This retrieval must be performed in a case-insensitive
     * manner.
     *
     * This method MUST return a empty string if the specified header is not
     * found in the message.
     *
     * @param string $name Header name.
     * @return string Comma-separated string of the header values or an empty
     *     string if the header is not found.
     */
    public function getHeaderLine(string $name): string;

    /**
     * Return an instance with the specified header. The header and its value(s)
     * will replace the existing header if it appears in the message.
     *
     * This method MUST be implemented to retain immutability of the message and
     * MUST return an instance with the added or updated header and its
     * value(s).
     *
     * @param string $name Header name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws InvalidArgumentException for invalid header names or values.
     */
    public function withHeader(string $name, $value);

    /**
     * Return an instance with the specified header. The header and its value(s)
     * will either be added or updated if it already appears in the message.
     *
     * This method MUST be implemented to retain immutability of the message and
     * MUST return an instance with the added or updated header and its
     * value(s).
     *
     * @param string $name Header name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader(string $name, $value);

    /**
     * Return an instance without the specified header. The header and its
     * value(s) will be removed from the message instance.
     *
     * This method MUST be implemented to retain immutability of the message and
     * MUST return an instance without the specified header.
     *
     * @param string $name Header name.
     * @return static
     */
    public function withoutHeader(string $name);

    /**
     * Retrieves the body of the message.
     *
     * @return StreamInterface Body of the message as a stream.
     */
    public function getBody(): StreamInterface;

    /**
     * Return an instance with the specified stream as the body.
     *
     * This method MUST be implemented to retain immutability of the message and
     * MUST return an instance with the specified stream as the body.
     *
     * @param StreamInterface $body Body as a stream.
     * @return static
     * @throws InvalidArgumentException if the specified body is invalid.
     */
    public function withBody(StreamInterface $body);
}
