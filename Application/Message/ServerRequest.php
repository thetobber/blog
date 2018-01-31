<?php
declare(strict_types = 1);
namespace Application\Message;

class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @var array
     */
    protected $serverParams;

    /**
     * @var array
     */
    protected $cookieParams;

    /**
     * @var array
     */
    protected $queryParams;

    /**
     * @var array
     */
    protected $uploadedFiles;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $parsedBody;

    /**
     * @var boolean
     */
    protected $isBodyParsed;

    public function __construct(
        string $method,
        string $protocolVersion,
        UriInterface $uri,
        array $headers,
        StreamInterface $body,
        array $serverParams,
        array $cookieParams,
        array $queryParams,
        array $uploadedFiles,
        array $attributes
    ) {
        parent::__construct($method, $protocolVersion, $uri, $headers, $body);
        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->queryParams = $queryParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->attributes = $attributes;
    }

    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies): array
    {
        $clone = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function withQueryParams(array $query): self
    {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles): self
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    /**
     * @return null|array|object
     */
    public function getParsedBody()
    {
        if ($this->isBodyParsed) {
            return $this->parsedBody;
        }

        if ($this->body === null) {
            return null;
        }

        $this->parsedBody = $this->parseBody();
        $this->isBodyParsed = true;

        return $this->parsedBody;
    }

    /**
     * @return null|array|object
     */
    protected function parseBody()
    {
        $contents = (string) $this->body;
        $mediaType = $this->getHeaderLine('content-type');

        if (empty($mediaType)) {
            return $contents;
        }

        if ($mediaType == 'application/x-www-form-urlencoded') {
            $parsed = [];
            parse_str($contents, $parsed);
            return $parsed;
        }

        if (strpos($mediaType, 'multipart/form-data') !== false) {
            return $_POST;
        }

        if ($mediaType == 'application/json' || $mediaType == 'text/json') {
            return json_decode($contents, true);
        }

        return $contents;
    }

    public function withParsedBody($data): self
    {
        if ($data !== null || !is_object($data) || !is_array($data)) {
            throw new InvalidArgumentException();
        }

        $clone = clone $this;
        $clone->bodyParsed = $data;

        return $clone;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @param mixed $value
     */
    public function withAttribute(string $name, $value): self
    {
        $clone = clone $this;
        $clone->attributes = [$name => $value] + $clone->attributes;

        return $clone;
    }

    public function withoutAttribute(string $name): self
    {
        $clone = clone $this;

        if (isset($clone->attributes[$name])) {
            unset($clone->attributes[$name]);
        }

        return $clone;
    }
}
