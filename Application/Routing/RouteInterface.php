<?php
declare(strict_types = 1);
namespace Application\Routing;

use Application\Message\ResponseInterface;

interface RouteInterface
{
    public function callAction(): ResponseInterface;
}
