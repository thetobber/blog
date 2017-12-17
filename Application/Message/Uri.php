<?php
namespace Application\Message;

use InvalidArgumentException;

/**
 * Represents an Uri <scheme>://<authority>[/<path>][?<query string>]
 */
class Uri
{
    /**
     * @var array
     */
    const SCHEMES = [
        '' => true,
        'http' => true,
        'https' => true
    ];

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int|null
     */
    protected $port;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var string
     */
    protected $fragment;

    public function __construct(
        string $scheme = '',
        string $host = '',
        ?int $port = null,
        string $user = '',
        string $password = '',
        string $path = '/',
        string $query = '',
        string $fragment = ''
    ) {

        $this->scheme = str_replace('://', '', strtolower($scheme));
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->path = (substr($path, -1) !== '/') ? "$path/" : $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    public function __toString(): string
    {
        $scheme = $this->getScheme();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return (empty($scheme) ? '' :  $scheme.'://').
            $this->getAuthority().
            $this->getPath().
            (empty($query) ? '' : '?'.$query).
            (empty($fragment) ? '' : '#'.$fragment);
    }

    public static function fromString(string $uri): self
    {
        if (($parts = parse_url($uri)) === false) {
            throw new InvalidArgumentException();
        }

        $path = $parts['path'] ?? '/';

        return new static(
            str_replace('://', '', strtolower($parts['scheme'] ?? '')),
            $parts['host'] ?? '',
            $parts['port'] ?? null,
            $parts['user'] ?? '',
            $parts['pass'] ?? '',
            (substr($path, -1) !== '/') ? "$path/" : $path,
            $parts['query'] ?? '',
            $parts['fragment'] ?? ''
        );
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(bool $withPort = true): string
    {
        $userInfo = $this->getUserInfo();
        $port = $withPort ? ':'.($this->port ?? '') : '';

        return (empty($userInfo) ? '' : $userInfo.'@').$this->host.$port;
    }

    public function getUserInfo(): string
    {
        return $this->user.(empty($this->password) ? '' : ':'.$this->password);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme(string $scheme): self
    {
        $scheme = str_replace('://', '', $scheme);
        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    public function withUserInfo(string $user, ?string $password = null): self
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password ?? '';

        return $clone;
    }

    public function withHost(string $host): self
    {
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    public function withPort(?int $port): self
    {
        if ($port !== null && ($port >= 1 && $port <= 65535)) {
            throw new InvalidArgumentException();
        }

        $port = $port;
        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    public function withPath(string $path): self
    {
        $path = (substr($path, -1) !== '/') ? "$path/" : $path;
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    public function withQuery(string $query): self
    {
        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    public function withFragment(string $fragment): self
    {
        $fragment = ltrim($fragment, '#');
        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }
}
