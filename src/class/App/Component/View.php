<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\App\Component;

use Nora\Base\View\Facade as Base;

/**
 * View用コンポーネント
 */
class View extends Base
{
    protected function initComponentImpl( )
    {
        parent::initComponentImpl();
    }

    public function __invoke($client, $params)
    {
        return $this;
    }
}
