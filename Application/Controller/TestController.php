<?php
namespace Application\Controller;

class TestController extends AbstractController
{
    public function index(): string
    {
        return $this->render('Index.php', [
            'test1' => 'Variable for testing.',
            'test2' => 'Hello world'
        ]);
    }
}
