<?php
declare(strict_types = 1);
namespace Application\Controller;

use Application\Message\ResponseInterface;

class TestController extends AbstractController
{
    public function index(): ResponseInterface
    {
        $model = [
            'test1' => 'Variable for testing.',
            'test2' => 'Hello world'
        ];

        return $this->view('../View/Index.php', $model, 200);
    }
}
