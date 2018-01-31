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
        include($filePath);
        return ob_get_clean();
    }

    protected function view(
        string $filePath,
        $model,
        int $statusCode = 200
    ): ResponseInterface {
        $contents = $this->render($filePath, $model);

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

        return $this->reponse->getBody()->write($contents);
    }

    protected function render(string $filePath, $model): string
    {
        ob_start();
        include(VIEW_DIR.'/'.$filePath);
        return ob_get_clean();
        //$contents = ob_get_clean();
        //return gzencode($contents, 9);
    }
}
