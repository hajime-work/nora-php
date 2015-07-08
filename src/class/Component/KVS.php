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
use Nora\Data\KVS\Facade as Base;


/**
 * KVSコンポーネント
 */
class KVS extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
        $this->injection([
            'Configure',
            'DataBase',
            function($c, $db) {
                $this->setDBHandler($db);
            }
        ]);
    }
}
