<?php
namespace Nora\Network\API\OAuth;

use Nora\Base\Component\Componentable;
use Nora\Base\Hash\Hash;

/**
 * OAuth Request
 */
class Oauth
{
    public function __construct( )
    {
    }

    public function createConsumer($spec)
    {
        return new Consumer($spec);
    }

    public function createToken($spec)
    {
        return new Token($spec);
    }
}
