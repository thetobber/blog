<?php
declare(strict_types = 1);
namespace Application\Middleware;

use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;

class Dispatcher
{
    protected $queue;

    public function __construct(array $queue)
    {
        $this->queue = $queue;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $current = array_shift($this->queue);
        $callable = $this->resolve($current);

        return $callable($request, $response, $this);
    }

    protected function resolve(?callable $action): callable
    {
        if ($action === null) {
            return function (
                ServerRequestInterface $request,
                ResponseInterface $response,
                callable $next
            ) {
                return $response;
            };
        }

        return $action;
    }
}
