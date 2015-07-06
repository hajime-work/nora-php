<?php
namespace Nora\Util;


/**
 * コネクションを表現するスペックライン
 *
 */
class Spec
{
    private $_attrs = [];
    private $_datas = [];

    public function __construct($string)
    {
        $this->parse($string);
    }

    public function setAttr($key, $value = null)
    {
        if (is_array($key))
        {
            foreach($key as $k=>$v) $this->setAttr($k, $v);
            return $this;
        }

        $this->_attrs[$key] = $value;
        return $this;
    }

    public function hasAttr($key)
    {
        return isset($this->_attrs[$key]);
    }

    public function getAttr($key, $value = null)
    {
        if ($this->hasAttr($key))
        {
            return $this->_attrs[$key];
        }
        return $value;
    }

    public function getAttrs()
    {
        return $this->_attrs;
    }

    protected function set($k, $v)
    {
        if ($k === 'attrs')
        {
            parse_str($v, $v);
            $this->setAttr($v);
        }else{
            $this->_datas[$k] = $v;
        }
        return $this;
    }

    public function get($k, $v = null)
    {
        if (!$this->has($k))
        {
            return $v;
        }
        return $this->_datas[$k];
    }

    public function has($k)
    {
        return isset($this->_datas[$k]);
    }

    public function __get($k)
    {
        return $this->get($k);
    }

    public function __isset($k)
    {
        return $this->has($k);
    }
}
