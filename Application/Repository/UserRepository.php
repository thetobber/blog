<?php
declare(strict_types = 1);
namespace Application\Repository;

use PDO;
use PDOException;
use Application\Model\Role;
use Application\Security\Authenticator;
use Application\Security\Validator;

class UserRepository extends AbstractRepository
{
    public function createUser(array $model): array
    {
        $errors = Validator::verifyCreateUser($model);

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $statement = $this->database->prepare('CALL CREATE_USER(?, ?, ?, ?)');

        $password = password_hash($model['password'], PASSWORD_BCRYPT);

        $statement->bindValue(1, Role::MEMBER, PDO::PARAM_INT);
        $statement->bindValue(2, $model['username'], PDO::PARAM_STR);
        $statement->bindValue(3, $model['email'], PDO::PARAM_STR);
        $statement->bindValue(4, $password);
        $result = [];

        try {
            if (!$statement->execute()) {
                $result['errors']['database'] = 'An unexpected error occurred.';
            }
        }
        catch (PDOException $exception) {
            if ($exception->getCode() == '23000') {
                $result['errors']['database'] = 'Username already exists.';
            }
            else {
                $result['errors']['database'] = 'An unexpected error occurred.';
            }
        }

        $statement->closeCursor();

        return $result;
    }

    public function getUser(string $username): array
    {
        $statement = $this->database->prepare('CALL GET_USER(?)');
        $statement->bindValue(1, $username, PDO::PARAM_STR);
        $result = [];

        try {
            if ($statement->execute()) {
                $model = $statement->fetch(PDO::FETCH_ASSOC);

                if ($model !== false) {
                    $result['content'] = $model;
                }
                // else {
                //     $result['errors']['not_found'] = true;
                // }
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

    public function updateUserPassword(array $model): array
    {
        $errors = Validator::verifyUpdatePassword($model);

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $model = array_merge($model, $this->getUser($model['username']));

        if (!isset($model['content'])) {
            $model['errors']['credentials'] = true;
            return $model;
        }

        $isValid = password_verify($model['old_password'], $model['content']['password']);

        if (!$isValid) {
            $model['errors']['credentials'] = true;
            return $model;
        }

        $statement = $this->database->prepare('CALL UPDATE_USER_PASSWORD(?, ?)');
        $password = password_hash($model['password'], PASSWORD_BCRYPT);
        $statement->bindValue(1, $model['username'], PDO::PARAM_STR);
        $statement->bindValue(2, $password);

        try {
            if (!$statement->execute()) {
                $model['errors']['database'] = 'An unexpected error occurred.';
            }
        }
        catch (PDOException $exception) {
            $model['errors']['database'] = 'An unexpected error occurred.';
        }

        $statement->closeCursor();

        return $model;
    }

    public function updateUserEmail(array $model): array
    {
        $errors = Validator::verifyUpdateEmail($model);

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $statement = $this->database->prepare('CALL UPDATE_USER_EMAIL(?, ?)');
        $statement->bindValue(1, $model['username'], PDO::PARAM_STR);
        $statement->bindValue(2, $model['email'], PDO::PARAM_STR);

        try {
            if (!$statement->execute()) {
                $model['errors']['database'] = 'An unexpected error occurred.';
            }
        }
        catch (PDOException $exception) {
            $model['errors']['database'] = 'An unexpected error occurred.';
        }

        $statement->closeCursor();

        $_SESSION['user']['email'] = $model['email'];

        return $model;
    }

    public function deleteUser(): array
    {

    }

    public function signIn(array $model): array
    {
        $errors = Validator::verifySignIn($model);

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $model = array_merge($model, $this->getUser($model['username']));

        if (!isset($model['content'])) {
            $model['errors']['credentials'] = true;
            return $model;
        }

        $isValid = password_verify($model['password'], $model['content']['password']);

        if (!$isValid) {
            $model['errors']['credentials'] = true;
            return $model;
        }

        Authenticator::signIn($model['content']);

        return $model;
    }
}
