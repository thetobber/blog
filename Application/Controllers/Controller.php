<?php
namespace Application\Controllers;

use Application\Libraries\AbstractController;

class Controller extends AbstractController
{
    public function index(): string
    {
        $model = [
            'test1' => 'Variable for testing.',
            'test2' => 'Hello world'
        ];

        return $this->render(dirname(__DIR__).'/Views/Index.php', $model);
    }
}