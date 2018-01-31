<?php
declare(strict_types = 1);
namespace Application\Controller;

use stdClass;
use Application\Message\ResponseInterface as Response;

class AuthController extends AbstractController
{
    public function signIn(): Response
    {
        return $this->view('Index.php', null, 200);
    }

    public function signOut(): Response
    {
        return $this->view('Index.php', null, 200);
    }

    public function getRegister(): Response
    {
        $model = 'GET';

        return $this->view('Auth/Register.php', $model, 200);
    }

    public function postRegister(): Response
    {
        $body = $this->request->getParsedBody();

        $validator = [
            isset($body['username']),
            isset($body['email']),
            isset($body['password']),
            isset($body['confirm'])
        ];



        $model = new stdClass();
        $model->content = $this->request->getParsedBody();

        return $this->view('Auth/Register.php', $model, 200);
    }
}
