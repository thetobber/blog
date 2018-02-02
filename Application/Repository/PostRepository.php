<?php
declare(strict_types = 1);
namespace Application\Repository;

use PDO;
use PDOException;
use Application\Model\Role;
use Application\Security\Authenticator;
use Application\Security\Validator;

class PostRepository extends AbstractRepository
{
    public function createPost(array $model): array
    {
        $errors = Validator::verifyCreatePost($model);

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $statement = $this->database->prepare('CALL CREATE_POST(?, ?, ?, ?)');
        $statement->bindValue(1, $model['title'], PDO::PARAM_STR);
        $statement->bindValue(2, $model['author'], PDO::PARAM_STR);
        $statement->bindValue(3, $model['owner'], PDO::PARAM_STR);
        $statement->bindValue(4, $model['content'], PDO::PARAM_STR);
        $result = [];

        try {
            if (!$statement->execute()) {
                $result['errors']['database'] = 'An unexpected error occurred.';
            }
        }
        catch (PDOException $exception) {
            $result['errors']['database'] = 'An unexpected error occurred.';
        }

        $statement->closeCursor();

        return $result;
    }

    public function updatePost(array $model): array
    {
    }

    public function deletePost(array $model): array
    {
    }

    public function getByAuthor(string $username): array
    {
        if (!$this->userExists($username)) {
            return ['not_found' => true];
        }

        $statement = $this->database->prepare('CALL GET_POSTS_BY_AUTHOR(?)');
        $statement->bindValue(1, $username, PDO::PARAM_STR);
        $result = [];

        try {
            if ($statement->execute()) {
                $model = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result['content'] = $model ?? [];
            }
            else {
                $result['errors']['database'] = 'An unexpected error occurred.';
            }
        }
        catch (PDOException $exception) {
            $result['errors']['database'] = 'An unexpected error occurred.';
        }

        $statement->closeCursor();

        return $result;
    }

    public function getByOwner(string $username): array
    {
        if (!$this->userExists($username)) {
            return ['not_found' => true];
        }

        $statement = $this->database->prepare('CALL GET_POSTS_BY_OWNER(?)');
        $statement->bindValue(1, $username, PDO::PARAM_STR);
        $result = [];

        try {
            if ($statement->execute()) {
                $model = $statement->fetchAll(PDO::FETCH_ASSOC);
                $result['content'] = $model ?? [];
            }
            else {
                $result['errors']['database'] = 'An unexpected error occurred.';
            }
        }
        catch (PDOException $exception) {
            $result['errors']['database'] = 'An unexpected error occurred.';
        }

        $statement->closeCursor();

        return $result;
    }

    public function userExists(string $username): bool
    {
        $statement = $this->database->prepare('CALL USER_EXISTS(?)');
        $statement->bindValue(1, $username, PDO::PARAM_STR);

        $result = false;

        try {
            if ($statement->execute()) {
                $model = $statement->fetch(PDO::FETCH_ASSOC);
                $result = (bool) $model['userExists'][0] ?? false;
            }
        }
        catch (PDOException $exception) {}

        $statement->closeCursor();

        return $result;
    }
}
