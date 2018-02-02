<?php
declare(strict_types = 1);
namespace Application\Repository;

use Application\Database;

abstract class AbstractRepository
{
    protected $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }
}
