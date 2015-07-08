<?php
namespace Nora\Network\API\OAuth;

use Nora\Base\Hash\Hash;
use Nora;

/**
 * OAuth Token
 */
class Token extends Hash
{
    private $spec;
    private $_key;
    private $_secret;

    public function __construct($spec)
    {
        $this->_key          = $spec['oauth_token'];
        $this->_secret       = $spec['oauth_token_secret'];

        $this->initValues($spec);
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
