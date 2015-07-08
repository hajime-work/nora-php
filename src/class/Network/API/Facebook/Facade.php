<?php
namespace Nora\Network\API\Facebook;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Util\Util;
use Nora;

/**
 * Facebook API 
 */
class Facade extends Component
{
    private $_oauth;
    private $_consumers;

    public function __construct( )
    {
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

        $this->_graph = new Graph();
        $this->_graph->initcomponent($this->scope()->newScope('Graph'));
    }

    public function oauth( )
    {
        return $this->_oauth;
    }

    public function graph( )
    {
        return $this->_graph;
    }
}
