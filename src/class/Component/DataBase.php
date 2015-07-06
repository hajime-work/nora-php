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
use Nora\Data\DataBase\Facade as Base;

/**
 * データベースコンポーネント
 */
class DataBase extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
        parent::initComponentImpl();

        $this->injection([
            'Configure',
            function($c) {
                foreach($c('database.connections', []) as $k=>$v)
                {
                    $this->setConnection($k, $v);
                }
            }
        ]);
    }
}
