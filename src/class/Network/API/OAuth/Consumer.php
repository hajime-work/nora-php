<?php
namespace Nora\Network\API\OAuth;

use Nora\Base\Hash\Hash;
use Nora;

/**
 * Twitter APP
 */
class Consumer extends Hash
{
    private $_key, $_secret;

    public function __construct($spec)
    {
        $this->_key          = $spec['key'];
        $this->_secret       = $spec['secret'];
    }

    public function key()
    {
        return $this->_key;
    }

    public function secret()
    {
        return $this->_secret;
    }
}
