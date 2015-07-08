<?php
namespace Nora\Component;

use Nora\Base\Component\Componentable;
use Nora\Network\API\Facebook\Facade as Base;

class Facebook extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
        parent::initComponentImpl();

        $this->injection([
            'Configure',
            'DataBase',
            function($c, $db) {
                $this->setConsumer($c('api.facebook.consumers'));
            }
        ]);
    }
}
