<?php
declare(strict_types = 1);
namespace Application\Middleware;

use RuntimeException;
use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;
use Application\Message\UriInterface;
use Application\Routing\Route;
use Application\Routing\RouteInterface;

class RouteMiddleware implements MiddlewareInterface
{
    protected $routes;

    public function __construct(array $routes = [])
    {
        $this->routes = $routes;
    }

    public function __invoke(Request $request, Response $response, callable $next = null): Response
    {
        $uri = $request->getUri();
        $route = $this->matchRoute($uri);

        $response = $route->callAction();

        return $next($request, $response);
    }

    private function matchRoute(UriInterface $uri): RouteInterface
    {
        foreach ($this->routes as $route) {
            if ($route->match($uri)) {
                return $route;
            }
        }

        throw new RuntimeException();
    }
}
