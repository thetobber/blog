<?php
declare(strict_types = 1);
namespace Application\Routing;

use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;
use Application\Message\UriInterface;

class Route
{
    protected $pattern;
    protected $method;
    protected $action;
    protected $parameters = [];

    public function __construct(string $pattern, string $method, callable $action)
    {
        $this->pattern = $pattern;
        $this->method = $method;
        $this->action = $action;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return call_user_func_array($this->action, [$request,  $response]);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function match(UriInterface $uri): bool {
        return preg_match($this->pattern, $uri->getPath(), $this->parameters) === 1;
    }
}
