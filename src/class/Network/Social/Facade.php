<?php
namespace Nora\Network\Social;

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
