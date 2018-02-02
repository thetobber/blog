<?php
declare(strict_types = 1);
namespace Application\Controller;

use stdClass;
use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;
use Application\Repository\UserRepository;
use Application\Security\Authenticator;

class AuthController extends AbstractController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function getSignIn(Request $request, Response $response): Response
    {
        return $this->view('/Auth/SignIn.php', $response);
    }

    public function postSignIn(Request $request, Response $response): Response
    {
        if (Authenticator::isAuthenticated()) {
            return $this->redirect('/profile', $response);
        }

        $body = $request->getParsedBody();
        $model = $this->repository->signIn($body);

        if (!isset($model['errors']['credentials'])) {
            return $this->redirect('/profile', $response);
        }

        return $this->view('/Auth/SignIn.php', $response, array_merge($model, $body));
    }

    public function postSignOut(Request $request, Response $response): Response
    {
        Authenticator::signOut();

        return $this->redirect('/signin', $response);
    }

    public function getRegister(Request $request, Response $response): Response
    {
        if (Authenticator::isAuthenticated()) {
            return $this->redirect('/profile', $response);
        }

        return $this->view('/Auth/Register.php', $response);
    }

    public function postRegister(Request $request, Response $response): Response
    {
        if (Authenticator::isAuthenticated()) {
            return $this->redirect('/profile', $response);
        }

        $body = $request->getParsedBody();
        $model = $this->repository->createUser($body);

        if (!isset($model['errors'])) {
            return $this->redirect('/signin', $response);
        }

        $model['content'] = $body;

        return $this->view('/Auth/Register.php', $response, $model);
    }

    public function getProfile(Request $request, Response $response): Response
    {
        if (!Authenticator::isAuthenticated()) {
            return $this->redirect('/signin', $response);
        }

        $model = $this->repository->getUser($_SESSION['user']['username']);

        return $this->view('/Auth/Profile.php', $response, $model);
    }

    public function postUpdatePassword(Request $request, Response $response): Response
    {
        if (!Authenticator::isAuthenticated()) {
            return $this->redirect('/signin', $response);
        }

        $body = $request->getParsedBody();
        $body['username'] = $_SESSION['user']['username'];

        $model = $this->repository->updateUserPassword($body);

        if (isset($model['errors'])) {
            return $this->view('/Auth/Profile.php', $response, $model);
        }

        $model['password_success'] = true;

        return $this->view('/Auth/Profile.php', $response, $model);
    }

    public function postUpdateEmail(Request $request, Response $response): Response
    {
        if (!Authenticator::isAuthenticated()) {
            return $this->redirect('/signin', $response);
        }

        $body = $request->getParsedBody();
        $body['username'] = $_SESSION['user']['username'];

        $model = $this->repository->updateUserEmail($body);

        if (isset($model['errors'])) {
            return $this->view('/Auth/Profile.php', $response, $model);
        }

        $model['email_success'] = true;

        return $this->view('/Auth/Profile.php', $response, $model);
    }
}
