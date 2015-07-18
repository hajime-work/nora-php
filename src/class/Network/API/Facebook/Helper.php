<?php
namespace Nora\Network\API\Facebook;

use Nora\Base\Event;
use Nora\Network\API;
use Nora\Base\Component\Component;
use Nora\Util\Util;
use Nora;

/**
 * Facebook API  Consumer Helper
 */
class Helper
{
    private $_facade;
    private $_consumer;
    private $_token;

    public function __construct(Facade $facade, API\OAuth\Consumer $consumer, API\OAuth\Token $token)
    {
        $this->_consumer = $consumer;
        $this->_token = $token;
        $this->_facade = $facade;
    }

    public function get($url, $params = [])
    {
        return $this->_facade->graph()->get($this->_token['access_token'], $url, $params);
    }
}
