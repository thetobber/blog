<?php
namespace Application;

use PDO;
use PDOException;

class DatabaseContext
{
    /**
    * @var PDO
    */
    private static $instance;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    'mysql:host=localhost;dbname=blog;charset=utf8',
                    'bloguser',
                    '123',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $exceoption) {
                header('HTTP/1.1 500 Internal Server Error', true);
                die('Could not connect to database.');
            }
        }

        return self::$instance;
    }
}
