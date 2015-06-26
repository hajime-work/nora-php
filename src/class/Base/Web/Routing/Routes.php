<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web\Routing;

use Nora\Base\Web\Controller;
use Nora\Base\Web\Request;
use Nora\Base\Hash\ObjectHash;
use Nora\Util\Util;

/**
 * ルーティングクラス
 *
 */
class Routes extends ObjectHash
{
    private $_routes;
    private $_current = false;
    private $_idx = false;

    public function __construct ( )
    {

    }

    private function _flatton(&$list = [])
    {
        foreach($this as $v)
        {
            // もしルータだったら
            if ($v instanceof Router)
            {
                $v->routes()->rewind();
            }
            $list[] = $v;
        }
        return $list;
    }

    public function rewind( )
    {
        $this->_routes = $this->_flatton();
        $this->_current = false;
        $this->_idx = 0;
    }

    public function current( )
    {
        if ($this->_idx === false)
        {
            $this->rewind();
        }

        if(!isset($this->_routes[$this->_idx]))
        {
            return false;
        }

        $this->_current = $this->_routes[$this->_idx];

        return $this->_current;
    }

    public function next( )
    {
        $this->_idx++;
    }

}
