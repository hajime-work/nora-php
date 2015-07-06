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
use Nora\Data\Model\Facade as Base;


/**
 * モデルコンポーネント
 */
class Model extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
        $this->injection([
            'Configure',
            'DataSource',
            function($c, $DS) {
                $this->setDataSourceHandler($DS);
            }
        ]);
    }
}
