<?php
declare(strict_types = 1);
namespace Application\Controller;

abstract class AbstractController
{
    protected function render(string $filePath, $model): string
    {
        ob_start();
        include(VIEW_DIR.'/'.$filePath);
        return ob_get_clean();
        //$contents = ob_get_clean();
        //return gzencode($contents, 9);
    }
}
