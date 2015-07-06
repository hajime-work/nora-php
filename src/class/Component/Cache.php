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
use Nora\Data\Cache\Facade as Base;

/**
 * キャッシュコンポーネント
 */
class Cache extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
        $this->injection([
            'Configure',
            'DataBase',
            function($c, $db) {
                $conf = $c('cache', []);
                $this->setDBHandler($db);
                $this->connect($conf['spec']);
            }
        ]);
    }
}
