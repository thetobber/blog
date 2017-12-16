<?php
namespace Application\Middleware;

use Application\Message\ServerRequestInterface;
use Application\Message\ResponseInterface;

interface MiddlewareInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ): ResponseInterface;
}
