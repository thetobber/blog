<?php
declare(strict_types = 1);
namespace Application\Controller;

use Application\Message\ServerRequestInterface;
use Application\Message\ResponseInterface;

abstract class AbstractController
{
    protected $request;
    protected $response;
    protected $attributes;
    protected $viewDir = __DIR__.'/../View/';

    public function __construct(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->attributes = $request->getAttributes();
    }

    protected function renderView(string $filePath, $model): string {
        ob_start();
        include($this->viewDir.$filePath);
        return ob_get_clean();
    }

    protected function view(
        string $filePath,
        $model = null,
        int $statusCode = 200
    ): ResponseInterface {
        $contents = $this->renderView($filePath, $model);

        return $this->writeResponse('text/html', $contents, $statusCode);
    }

    protected function writeResponse(
        string $mimeType,
        string $contents,
        int $statusCode = 200
    ): ResponseInterface {
        $this->response = $this->response
            ->withHeader('Content-Type', $mimeType)
            ->withStatus($statusCode);

        $this->response->getBody()->write($contents);

        return $this->response;
    }
}
