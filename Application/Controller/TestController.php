<?php
declare(strict_types = 1);
namespace Application\Controller;

use Application\Message\ResponseInterface as Response;

class TestController extends AbstractController
{
    public function index(): Response
    {
        $model = [
            'test1' => 'Variable for testing.',
            'test2' => 'Hello world'
        ];

        return $this->view('Index.php', $model, 200);
    }

    public function notFound(): Response
    {
        return $this->view('404.php');
    }
}
