<?php
declare(strict_types = 1);
namespace Application;

use PDO;
use PDOException;

class Database
{
    private static $instance;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(CONNECTION_STRING, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
            } catch (PDOException $exceoption) {
                header('HTTP/1.1 500 Internal Server Error', true);
                die('Could not connect to database.');
            }
        }

        return self::$instance;
    }
}
