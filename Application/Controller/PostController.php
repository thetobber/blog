<?php
declare(strict_types = 1);
namespace Application\Controller;

use Application\Message\ServerRequestInterface as Request;
use Application\Message\ResponseInterface as Response;
use Application\Repository\PostRepository;
use Application\Security\Authenticator;

class PostController extends AbstractController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new PostRepository();
    }

    public function getByAuthor(Request $request, Response $response): Response
    {
        if (!Authenticator::isAuthenticated()) {
            return $this->redirect('/signin', $response);
        }

        $model = $this->repository->getByAuthor($_SESSION['user']['username']);

        if (isset($model['not_found']) || isset($model['errors']['database'])) {
            return $this->view('/404.php', $response);
        }

        return $this->view('/Post/Author.php', $response, $model);
    }

    public function getByOwner(Request $request, Response $response): Response
    {
        $params = $request->getAttribute('params');
        $model = $this->repository->getByOwner($params['username']);

        if (isset($model['not_found']) || isset($model['errors']['database'])) {
            return $this->view('/404.php', $response);
        }

        $model['owner'] = $params['username'];

        return $this->view('/Post/Owner.php', $response, $model);
    }

    public function postToOwner(Request $request, Response $response): Response
    {
        if (!Authenticator::isAuthenticated()) {
            return $this->redirect('/signin', $response);
        }

        $params = $request->getAttribute('params');
        $userExists = $this->repository->userExists($params['username']);

        if (!$userExists) {
            return $this->view('/404.php', $response);
        }

        $body = $request->getParsedBody();

        $body['author'] = $_SESSION['user']['username'];
        $body['owner'] = $params['username'];
        $model = $this->repository->createPost($body);

        $model['author'] = $_SESSION['user']['username'];
        $model['owner'] = $params['username'];

        if (isset($model['errors'])) {
            $board = $this->repository->getByOwner($params['username']);

            return $this->view('/Post/Owner.php', $response, array_merge($model, $board));
        }

        return $this->redirect('/person/'.urlencode($params['username']), $response);
    }
}
