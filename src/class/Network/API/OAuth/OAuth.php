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

    public function createConsumer($key, $secret)
    {
        return new Consumer($key, $secret);
    }

    public function createToken($key, $secret)
    {
        return new Token($key, $secret);
    }
}
