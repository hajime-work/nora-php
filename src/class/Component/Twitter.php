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

use Nora\Network\API\Twitter\Facade as Base;

/**
 * Twitter
 */
class Twitter extends Base
{
    private $_account;

    protected function initComponentImpl( )
    {
        parent::initComponentImpl();

        $this->injection([
            'Configure',
            function($c) {
                $this->setConsumer($c('api.twitter.consumers'));
            }
        ]);
    }


}
