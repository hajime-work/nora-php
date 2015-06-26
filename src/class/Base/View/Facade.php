<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\View;

use Nora\Base\Component\Componentable;
use Nora;

/**
 * View Facade
 *
 */
class Facade
{
    use Componentable;

    protected function initComponentImpl( )
    {
    }

    public function render($file, $params = [])
    {
        var_dump($file);
    }
}
