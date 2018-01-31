<?php
declare(strict_types=1);
namespace Application\Message;

use InvalidArgumentException;

/**
 * Represents an Uri <scheme>://<authority>[/<path>][?<query string>]
 */
class Uri implements UriInterface
{
    /**
     * @var array
     */
    protected const SCHEMES = [
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

        $this->scheme = $this->filterScheme($scheme);
        $this->host = $host;
        $this->port = $this->filterPort($port);
        $this->user = $user;
        $this->password = $password;
        $this->path = $this->filterPath($path);
        $this->query = $this->filterQuery($query);
        $this->fragment = $this->filterQuery($fragment);
    }

    public function __toString(): string
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return ($scheme ? "$scheme:" : '').
            ($authority ? "//$authority" : '').
            rtrim($this->getPath(), '/').
            ($query ? "?$query" : '').
            ($fragment ? "#$fragment" : '');
    }

    public static function fromString(string $uri): self
    {
        if (($parts = parse_url($uri)) === false) {
            throw new InvalidArgumentException();
        }

        return new static(
            $parts['scheme'] ?? '',
            $parts['host'] ?? '',
            $parts['port'] ?? null,
            $parts['user'] ?? '',
            $parts['pass'] ?? '',
            $parts['path'] ?? '/',
            $parts['query'] ?? '',
            $parts['fragment'] ?? ''
        );
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function withScheme(string $scheme): self
    {
        $clone = clone $this;
        $clone->scheme = $this->filterScheme($scheme);

        return $clone;
    }

    protected function filterScheme(string $scheme): string
    {
        $scheme = str_replace('://', '', strtolower($scheme));

        if (!isset(self::SCHEMES[$scheme])) {
            throw new InvalidArgumentException();
        }

        return $scheme;
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

    public function withUserInfo(string $user, ?string $password = null): self
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password ?? '';

        return $clone;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function withHost(string $host): self
    {
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function withPort(?int $port): self
    {
        $clone = clone $this;
        $clone->port = $this->filterPort($port);

        return $clone;
    }

    protected function filterPort(?int $port): ?int
    {
        if ($port === null || ($port >= 1 && $port <= 65535)) {
            return $port;
        }

        throw new InvalidArgumentException();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function withPath(string $path): self
    {
        $clone = clone $this;
        $clone->path = $this->filterPath($path);

        return $clone;
    }

    protected function filterPath(string $path): string
    {
        if ($path === '' || $path === '/') {
            return '/';
        }

        $path = preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );

        return '/'.ltrim(rtrim($path, '/'), '/');
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function withQuery(string $query): self
    {
        $query = $this->filterQuery($query);
        $clone = clone $this;
        $clone->query = ltrim($query, '?');

        return $clone;
    }

    protected function filterQuery($query)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $query
        );
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withFragment(string $fragment): self
    {
        $fragment = $this->filterQuery($fragment);
        $clone = clone $this;
        $clone->fragment = ltrim($fragment, '#');

        return $clone;
    }
}
