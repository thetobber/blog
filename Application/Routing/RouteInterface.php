<?php
declare(strict_types = 1);
namespace Application\Routing;

use Application\Message\ResponseInterface as Response;

interface RouteInterface
{
    public function callAction(): Response;
}
