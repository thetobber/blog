<?php
declare(strict_types = 1);
namespace Application\Security;

class CsrfValidator
{
    public static function getToken(): string
    {
        $token = bin2hex(random_bytes(10));

        $_SESSION['csrf'] = [
            'token' => $token,
            'time' => time()
        ];

        return $token;
    }

    public static function verifyToken(string $token): bool
    {
        if (!isset($_SESSION['csrf'])) {
            return false;
        }

        if ((time() - $_SESSION['csrf']['time']) > 300) {
            session_regenerate_id();
            return false;
        }

        if (strcmp($_SESSION['csrf']['token'], $token) === 0) {
            return true;
        }

        // Should also check HTTP Origin and Referer headers

        return false;
    }
}
