<?php
declare(strict_types = 1);
namespace Application\Middleware;

use RuntimeException;
use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;
use Application\Message\UriInterface;
use Application\Routing\Route;

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
        $route = $this->matchRoute($request);

        $request = $request->withAttribute('params', $route->getParameters());

        return $next($request, $route($request, $response));
    }

    private function matchRoute(Request $request): Route
    {
        $uri = $request->getUri();
        $requestMethod = strtolower($request->getMethod());

        foreach ($this->routes as $route) {
            $method = strtolower($route->getMethod());

            if ($route->match($uri) && ($method === 'all' || $requestMethod === $method)) {
                return $route;
            }
        }

        throw new RuntimeException();
    }
}
