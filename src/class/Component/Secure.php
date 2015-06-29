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
use Nora\Base\Secure\Facade as Base;

/**
 * セキュリティモジュール
 */
class Secure extends Base
{
    use Component\Componentable;

    protected function initComponentImpl( )
    {
        parent::initComponentImpl();
    }
}
