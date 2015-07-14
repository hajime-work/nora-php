<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Secure;

use Nora\Base\Component\Componentable;
use Nora;

/**
 * View Facade
 *
 */
class Facade
{
    use Componentable;

    public static $salt = 'NORA';

    protected function initComponentImpl( )
    {
    }


    public function password( )
    {
        return Password::create($this, [
            'salt' => 'NORA',
            'saltCount' =>  10,
            'stretch' => 1000
        ]);
    }

    public function random( )
    {
        return new Random($this);
    }

    public function auth( )
    {
        return new Auth($this);
    }
}
