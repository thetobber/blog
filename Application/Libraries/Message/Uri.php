<?php
namespace Application\Libraries\Message;

use InvalidArgumentException;

/**
 * Represents an Uri <scheme>://<authority>[/<path>][?<query string>]
 */
class Uri
{
    /**
     * The accepted Uri schemes for this implmentation.
     *
     * @var array
     */
    const SCHEMES = [
        '' => true,
        'http' => true,
        'https' => true
    ];

    /**
     * Scheme part of the Uri <scheme>:// which would be empty, HTTP or HTTPS.
     * There is a lot of different schemes but onlye these will be used for
     * implementation.
     *
     * @var string
     */
    protected $scheme;
    
    /**
     * The host part of the authority. This can contain an username and password
     * for authentication in addition to a port number. The <authority> is
     * composed like so [user[:password]@]host[:port] and keep in mind that an
     * ip address is a valid host name.
     *
     * @var string
     */
    protected $host;
    
    /**
     * The port number is part of the authority. If no port is supplied it can
     * be seen as using the default port :80 or :443 for an encrypted request.
     *
     * @see self::$host
     * @var int|null
     */
    protected $port;
    
    /**
     * The optional username part of the Uri for authentication. This can be
     * combined with a password user[:pass] though it's not recommended.
     *
     * @var string
     */
    protected $user;
    
    /**
     * The optional password part of the Uri which would require an username.
     * It's strongly recommended not including the password in the Uri.
     *
     * @var string
     */
    protected $password;
    
    /**
     * The request path of the Uri is composed of values separated by slashes
     * [/<path>] which is usually used by the server to navigate directories.
     *
     * @var string
     */
    protected $path;
    
    /**
     * The optional query parameters of the Uri that is passed together with the
     * request. These parameters are usually parsed and can have different
     * compositions depending on the backend language. Some languages support
     * arrays or associative arrays to be passed in the query.
     *
     * PHP would parse ?array[]=a&array[]=b&array[subarray]=c as such:
     *
     * array(
     *     'array' => array(
     *         'a',
     *         'b',
     *         'subarray' => array(
     *             'c'
     *         )
     *     )
     * )
     *
     * @var string
     */
    protected $query;
    
    /**
     * The fragment part the Uri is usually not sent to the server but rather
     * used by the browser to link to elements within the same page.
     *
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

    /**
     * Attempts to parse the given Uri string and create a new instance of Uri.
     *
     * @param string $uri The Uri string to parse.
     * @return self
     * @throws InvalidArgumentException on malformed Uri.
     */
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

    /**
     * Get the scheme of the Uri.
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Get the authority part the Uri.
     *
     * @param bool $withPort Whether to include the port or not.
     * @return string Returns the full authority part of the Uri.
     */
    public function getAuthority(bool $withPort = true): string
    {
        $userInfo = $this->getUserInfo();
        $port = $withPort ? ':'.($this->port ?? '') : '';

        return (empty($userInfo) ? '' : $userInfo.'@').$this->host.$port;
    }

    /**
     * Get the user information from the authority part of the Uri.
     *
     * @return string Returns the username and password if set.
     */
    public function getUserInfo(): string
    {
        return $this->user.(empty($this->password) ? '' : ':'.$this->password);
    }

    /**
     * Get the host name from the authority part of the Uri. Keep in mind that
     * this kan also be an ip address.
     *
     * @return string Returns the host name.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Get the port number from the authority part of the Uri if it exists.
     *
     * @return int|null Returns the port number or null.
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Get the path from the Uri.
     *
     * @return string Returns the path or an empty string.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the query parameters from the Uri.
     *
     * @return string Returns the query parameter or an empty string.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Get the fragment from the Uri. Would usually be empty because the browser
     * use this to navigate within a giuven HTML document and omit to send this
     * to the server on a request.
     *
     * @return string Returns the fragment or an empty string.
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * Clone the instance and set a new Uri scheme.
     *
     * @param string $scheme The desired Uri scheme.
     * @return self Returns a cloned instance with a new Uri scheme.
     */
    public function withScheme(string $scheme): self
    {
        $scheme = str_replace('://', '', $scheme);

        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * Clone the instance and set a user and optionally a password.
     *
     * @param string $user The desired usernam.
     * @param string|null $password The desired password.
     * @return self Returns a cloned instance with a user and password.
     */
    public function withUserInfo(string $user, ?string $password = null): self
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password ?? '';

        return $clone;
    }

    /**
     * Clone the instance and set a new host.
     *
     * @param string $host The desired host.
     * @return self Returns a cloned instance with a new host.
     */
    public function withHost(string $host): self
    {
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    /**
     * Clone the instance and set a new port number.
     *
     * @param int|null $port The desired port number.
     * @return self Returns a cloned instance with a new post number.
     * @throws InvalidArgumentException if not in TCP and UDP port ranges.
     */
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

    /**
     * Clone the instance and set a new path.
     *
     * @param string $path The desired path.
     * @return self Returns a cloned instance with a new path.
     */
    public function withPath(string $path): self
    {
        $path = (substr($path, -1) !== '/') ? "$path/" : $path;

        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * Clone the instance and set new query parameters.
     *
     * @param string $query The desired query parameters.
     * @return self Returns a cloned instance with new query parameters.
     */
    public function withQuery(string $query): self
    {
        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    /**
     * Clone the instance and set a new fragment.
     *
     * @param string $fragment The desired fragment.
     * @return self Returns a cloned instance with a new fragment.
     */
    public function withFragment(string $fragment): self
    {
        $fragment = ltrim($fragment, '#');

        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    /**
     * Combine all the Uri parts and return it as a string.
     *
     * @return string Returns the full Uri as a string.
     */
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
}
