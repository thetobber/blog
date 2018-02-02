<?php
declare(strict_types = 1);
namespace Application\Model;

class Role
{
    public const ADMINISTRATOR = 1;
    public const MODERATOR = 2;
    public const MEMBER = 3;

    private function __construct() {}
}
