<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Component;

use Nora\Base\Component;
use Nora\Base\Hash;

/**
 * クッキー
 */
class Cookie extends Hash\Hash
{
    use Component\Componentable;

    protected function initComponentImpl( )
    {
        $this->initValues($_COOKIE);
    }
}
