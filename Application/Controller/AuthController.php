<?php
declare(strict_types = 1);
namespace Application\Controller;

use Application\Message\ResponseInterface as Response;

class AuthController extends AbstractController
{
    public function signIn(): Response
    {
        return $this->view('Index.php', $model, 200);
    }

    public function signOut(): Response
    {
        return $this->view('Index.php', $model, 200);
    }
}
