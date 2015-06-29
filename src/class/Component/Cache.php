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

use Nora\Base\Component\Componentable;
use Nora\Base\Cache\Facade as Base;

/**
 * キャッシュコンポーネント
 */
class Cache extends Base
{
    protected function initComponentImpl( )
    {
        parent::initComponentImpl();

        $this->injection([
            'Configure',
            function($c) {
                $conf = $c('cache', []);
                $this->initCache($conf);
            }
        ]);
    }
}
