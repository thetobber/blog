<?php
namespace Application\Libraries;

abstract class AbstractController
{
    protected function render(string $filePath, $model): string
    {
        ob_start();
        include($filePath);
        return ob_get_clean();
        //$contents = ob_get_clean();
        //return gzencode($contents, 9);
    }
}