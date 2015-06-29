<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\View\Engine;

use Nora\Base\Component\Component;

/**
 * View Rendering Engine
 *
 */
abstract class Base extends Component
{
    public function initComponentImpl()
    {
        $this->scope('view')->owner;
    }

    abstract public function render($file, $params);
}
