<?php
declare(strict_types = 1);
namespace Application\Routing;

use Application\Message\ResponseInterface;
use Application\Message\UriInterface;

class Route implements RouteInterface
{
    protected $pattern;
    protected $action;

    public function __construct(string $pattern, callable $action)
    {
        $this->pattern = $pattern;
        $this->action = $action;
    }

    public function callAction(): ResponseInterface
    {
        return call_user_func($this->action);
    }

    public function match(UriInterface $uri): bool {
        return preg_match($this->pattern, $uri->getPath()) === 1;
    }
}
