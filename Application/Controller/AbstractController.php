<?php
declare(strict_types = 1);
namespace Application\Controller;

use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;

abstract class AbstractController
{
    protected function view(string $filePath, Response $response, $model = null): Response
    {
        $contents = $this
            ->render($filePath, $model);

        $response
            ->getBody()
            ->write($contents);

        $size = $response
            ->getBody()
            ->getSize();

        $response = $response
            ->withHeader('Content-Type', 'text/html;charset=UTF-8')
            ->withHeader('Content-Length', (string) $size)
            ->withStatus(200);

        return $response;
    }

    protected function redirect(string $location, Response $response)
    {
        $response = $response
            ->withHeader('Location', $location)
            ->withStatus(302);

        return $response;
    }

    protected function render(string $filePath, $model): string
    {
        ob_start();
        include VIEW_DIR.$filePath;

        return ob_get_clean();
    }
}
