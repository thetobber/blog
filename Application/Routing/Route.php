<?php
declare(strict_types = 1);
namespace Application\Libraries\Routing;

class Route
{
    protected $action;

    public function __construct(callable $action)
    {
        $this->action = $action;
    }

    public function callAction()
    {
        return \call_user_func($this->action);
    }
}
