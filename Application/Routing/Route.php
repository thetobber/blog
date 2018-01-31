<?php
declare(strict_types = 1);
namespace Application\Routing;

use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;
use Application\Message\UriInterface;

class Route implements RouteInterface
{
    protected $pattern;
    protected $method;
    protected $action;

    public function __construct(string $pattern, string $method, callable $action)
    {
        $this->pattern = $pattern;
        $this->method = $method;
        $this->action = $action;
    }

    public function callAction(): Response
    {
        return call_user_func($this->action);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function match(UriInterface $uri): bool {
        return preg_match($this->pattern, $uri->getPath()) === 1;
    }
}
