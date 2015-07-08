<?php
namespace Nora\Network\API\Twitter;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Util\Util;
use Nora;
use Nora\Network\API\OAuth\Consumer;
use Nora\Network\API\OAuth\Token;

/**
 * Twitter API 
 */
class Facade extends Component
{
    private $_loader;
    private $_apps;
    private $_oauth;
    private $_consumers;

    public function __construct( )
    {
        $this->_loader = Util::InstanceLoader(function($key) {
            return $this->createApp($key);
        });
        $this->_apps = Nora::Hash();
        $this->_consumers = Nora::Hash();
    }

    public function consumer($name = 'default')
    {
        return $this->_consumers[$name];
    }

    public function setConsumer($name, $spec = null)
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v)
            {
                $this->setConsumer($k, $v);
            }
            return $this;
        }
        $this->_consumers[$name] = $this->_oauth->createConsumer($spec);
    }

    public function token($spec)
    {
        return $this->_oauth->createToken($spec);
    }

    protected function initComponentImpl( )
    {
        $this->_oauth = new OAuth();
        $this->_oauth->initcomponent($this->scope()->newScope('OAuth'));
    }

    public function oauth( )
    {
        return $this->_oauth;
    }

    /**
     * アクセスヘルパ
     */
    public function connect(Consumer $consumer, Token $token)
    {
        return new Helper($this, $consumer, $token);
    }
}
