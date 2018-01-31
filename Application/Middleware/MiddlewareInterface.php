<?php
declare(strict_types = 1);
namespace Application\Middleware;

use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;

interface MiddlewareInterface
{
    public function __invoke(Request $request, Response $response): Response;
}
