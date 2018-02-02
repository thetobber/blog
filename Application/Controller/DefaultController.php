<?php
declare(strict_types = 1);
namespace Application\Controller;

use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;

class DefaultController extends AbstractController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->view('/Index.php', $response);
    }

    public function notFound(Request $request, Response $response): Response
    {
        return $this->view('/404.php', $response);
    }
}
