<?php
declare(strict_types = 1);
namespace Application\Security;

class Authenticator
{
    public static function signIn(array $user): void
    {
        session_regenerate_id();

        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'verified' => $user['verified']
        ];
    }

    public static function signOut(): void
    {
        session_destroy();
        session_start();
        session_regenerate_id();
    }

    public static function isAuthenticated(): bool
    {
        // return isset($_SESSION['user']) && $_SESSION['user']['verified'] === true;
        return isset($_SESSION['user']);
    }

    public static function hasRole(int $role): bool
    {
        return self::isAuthenticated() && $_SESSION['role'] === $role;
    }

    public static function getRandomString(int $length): string
    {
        return bin2hex(random_bytes($length));
    }
}
