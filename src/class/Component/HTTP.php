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

use Nora\Network\HTTP\Facade as Base;

/**
 * HTTPコンポーネント
 */
class HTTP extends Base
{
    protected function initComponentImpl( )
    {
        parent::initComponentImpl();

        $this->injection([
            'Configure',
            function($c) {
                foreach($c('http', []) as $k=>$v)
                {

                }
            }
        ]);
    }
}
