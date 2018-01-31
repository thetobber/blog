<?php
declare(strict_types = 1);
namespace Application\Controller;

use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;

abstract class AbstractController
{
    protected $request;
    protected $response;
    protected $attributes;
    protected $viewDir = __DIR__.'/../View/';

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->attributes = $request->getAttributes();
    }

    protected function renderView(string $filePath, $model): string
    {
        ob_start();
        include($this->viewDir.$filePath);

        return ob_get_clean();
    }

    protected function view(string $filePath, $model = null, int $statusCode = 200): Response
    {
        $contents = $this->renderView($filePath, $model);

        return $this->writeResponse('text/html;charset=UTF-8', $contents, $statusCode);
    }

    protected function writeResponse(string $mimeType, string $contents, int $statusCode = 200): Response
    {
        $this->response->getBody()->write($contents);
        $size = (string) $this->response->getBody()->getSize();

        $this->response = $this->response
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Content-Length', $size)
            ->withStatus($statusCode);

        return $this->response;
    }
}
