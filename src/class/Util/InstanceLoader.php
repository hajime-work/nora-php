<?php
Namespace Nora\Util;

use Nora;

/**
 * インスタンスローダ
 */
class instanceLoader
{
    private $_cache = [];
    private $_factory;

    public function __construct($cb)
    {
        $this->_cache = Nora::Hash();
        $this->_factory = $cb;
    }

    public function get($name)
    {
        if (!isset($this->_cache[$name]))
        {
            $this->_cache[$name] =  $this->create($name);
        }

        return $this->_cache[$name];
    }

    public function create($name)
    {
        return call_user_func($this->_factory, $name);
    }
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
