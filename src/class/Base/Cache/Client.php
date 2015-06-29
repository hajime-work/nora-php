<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Cache;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * キャッシュ
 *
 */
class Client
{
    private $_prefix;
    private $_handler;

    public function __construct($handler, $prefix = null)
    {
        $this->_handler = $handler;
        $this->_prefix  = $prefix;
    }

    protected function setPrefix($string)
    {
        $this->_prefix = $string;
        return $this;
    }

    protected function setHandler($handler)
    {
        $this->_handler = $handler;
        return $this;
    }

    public function __get($name)
    {
        return new Client($this, $this->_prefix.'_'.$name);
    }

    public function set($name, $key)
    {
        $this->_handler->set($this->_prefix.'_'.$name, $key);
    }

    public function get($name)
    {
        return $this->_handler->get($this->_prefix.'_'.$name);
    }

    /**
     * UseCache
     *
     * @param string $name
     * @param callable $callback
     * @param int $expire_at
     * @param int $create_after
     */
    public function useCache ($name, $callback, $expire_at = -1, $create_after = -1)
    {
        return $this->_handler->useCache($this->_prefix.'_'.$name, $callback, $expire_at, $create_after);
    }
}
