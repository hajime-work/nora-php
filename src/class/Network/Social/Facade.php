<?php
namespace Nora\Network\Social;
use Nora\Base\Component\Component;

class Facade extends Component
{
    protected function initComponentImpl( )
    {
    }

    public function ShareLink( )
    {
        return new SocialLink( );
    }
}
