<?php
namespace Nora\Network\HTTP;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Network\API\Twitter;

/**
 * HTTP Client
 */
class Facade extends Component
{
    protected function initComponentImpl( )
    {
    }

    public function client ( )
    {
        return Client::createComponent($this->scope()->newScope('Client'));
    }
}
