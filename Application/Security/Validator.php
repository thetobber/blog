<?php
declare(strict_types = 1);
namespace Application\Security;

use Application\Security\CsrfValidator;

class Validator
{
    public static function verifyCreateUser(array $user): array
    {
        $model = [
            'token' => '',
            'username' => '',
            'email' => '',
            'password' => '',
            'confirm' => ''
        ];

        $model = array_merge($model, $user);

        $errors = [];

        if (!CsrfValidator::verifyToken($model['token'])) {
            $errors['token'] = true;
        }

        if (!self::verifyUsername($model['username'])) {
            $errors['username'] = true;
        }

        if (!self::verifyEmail($model['email'])) {
            $errors['email'] = true;
        }

        if (!self::verifyPassword($model['password'])) {
            $errors['password'] = true;
        }
        else if (!self::verifyEquality($model['password'], $model['confirm'])) {
            $errors['confirm'] = true;
        }

        return $errors;
    }

    public static function verifySignIn(array $user): array
    {
        $model = [
            'token' => '',
            'username' => '',
            'password' => ''
        ];

        $model = array_merge($model, $user);

        $errors = [];

        if (!CsrfValidator::verifyToken($model['token'])) {
            $errors['token'] = true;
        }

        if (!self::verifyUsername($model['username']) || !self::verifyPassword($model['password'])) {
            $errors['credentials'] = true;
        }

        return $errors;
    }

    public static function verifyUpdatePassword(array $user): array
    {
        $model = [
            'token' => '',
            'old_password' => '',
            'password' => '',
            'confirm' => ''
        ];

        $model = array_merge($model, $user);

        $errors = [];

        if (!CsrfValidator::verifyToken($model['token'])) {
            $errors['token'] = true;
        }

        if (!self::verifyPassword($model['old_password'])) {
            $errors['old_password'] = true;
        }

        if (!self::verifyPassword($model['password'])) {
            $errors['password'] = true;
        }
        else if (!self::verifyEquality($model['password'], $model['confirm'])) {
            $errors['confirm'] = true;
        }

        return $errors;
    }

    public static function verifyUpdateEmail(array $data): array
    {
        $model = [
            'token' => '',
            'email' => ''
        ];

        $model = array_merge($model, $data);

        $errors = [];

        if (!CsrfValidator::verifyToken($model['token'])) {
            $errors['token'] = true;
        }

        if (!self::verifyEmail($model['email'])) {
            $errors['email'] = true;
        }

        return $errors;
    }

    public static function verifyUsername(string $username): bool
    {
        return preg_match('@^.{1,191}$@u', $username) === 1;
    }

    public static function verifyEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function verifyPassword(string $password): bool
    {
        return preg_match('@^(?=.{8,})(?=.*[\p{Ll}])(?=.*[\p{Lu}])(?=.*[\d]).*$@u', $password) === 1;
    }

    public static function verifyEquality(string $a, string $b): bool
    {
        return $a === $b;
    }
}
