<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Configuration;

use Nora\Base\Hash;
use ArrayAccess;

/**
 * 設定管理アイテム[配列]
 */
class ItemAggregate extends Item implements ArrayAccess
{
    private $_values;

    public function __construct($data = [])
    {
        $this->_values = Hash\Hash::newHash(
            [],
            Hash\Hash::OPT_IGNORE_CASE |
            Hash\Hash::OPT_ALLOW_UNDEFINED_KEY_SET |
            Hash\Hash::OPT_ALLOW_UNDEFINED_KEY_GET
        );
        $this->setValue($data);
    }

    /**
     * 値をセットする
     *
     * @param array $data
     */
    public function setValue($data)
    {
        $this->write($data);
    }

    /**
     * 設定を書き込む
     *
     * @param string $name
     * @param mixed $value
     */
    public function write($name, $value = null)
    {
        if (is_array($name)){
            foreach($name as $k=>$v)
            {
                $this->write($k, $v);
            }
            return $this;
        }

        if (false !== $p = strpos($name, self::SEP))
        {
            $c = substr($name, 0, $p);
            $n = substr($name, $p+1);

            $value = [
                $n => $value
            ];

            if ($this->hasItem($c))
            {
                $this->getItem($c)->setValue($value);
            }else{
                $this->setItem($c, $this->createItem($value));
            }
        }else{
            $this->setItem($name, $this->createItem($value));
        }
        return $this;
    }

    /**
     * 設定があるか
     *
     * @param string $name
     * @param string $default
     * @param mixed $value
     */
    public function has($name)
    {
        if (false !== $p = strpos($name, self::SEP))
        {
            $c = substr($name, 0, $p);
            $n = substr($name, $p+1);


            if ($this->hasItem($c))
            {
                return $this->getItem($c)->hasItem($n);
            }
            return false;
        }

        if ($this->hasItem($name))
        {
            return true;
        }

        return false;
    }

    /**
     * 設定を消す
     *
     * @param string $name
     * @return void
     */
    public function del($name)
    {
        if (false !== $p = strpos($name, self::SEP))
        {
            $c = substr($name, 0, $p);
            $n = substr($name, $p+1);

            $value = [
                $n => $value
            ];

            if ($this->hasItem($c))
            {
                return $this->getItem($c)->del($n);
            }
            return true;
        }

        if ($this->hasItem($name))
        {
            $this->delItem($name);
            return true;
        }

        return true;
    }

    /**
     * 設定を読み込む
     *
     * @param string $name
     * @param string $default
     * @param mixed $value
     */
    public function read($name = null, $default = null)
    {
        if ($name === null)
        {
            return $this->toArray();
        }

        if (false !== $p = strpos($name, self::SEP))
        {
            $c = substr($name, 0, $p);
            $n = substr($name, $p+1);

            if ($this->hasItem($c))
            {
                return $this->returnValue($this->getItem($c)->read($n, $default), $default);
            }
            return $default;
        }

        if ($this->hasItem($name))
        {
            return $this->returnValue($this->getItem($name)->read(), $default);
        }

        return $default;
    }

    private function returnValue($data, $default)
    {
        if (is_array($default) && !is_array($data)) {
            if (empty($data)) return [];
            return [$data];
        }
        return $data;
    }



    /**
     * 設定を追記
     *
     * @param string $name
     * @param mixed $value
     */
    public function append($name, $value = null)
    {
        if ( $this->has($name) )
        {
            $data = $this->read($name);
            if (is_string($data)) $data = [$data];
            $data[] = $value;
            $this->write($name, $data);
        }else{
            $this->write($name, [$value]);
        }
        return $this;
    }


    protected function hasItem($key)
    {
        return $this->_values->hasVal($key);
    }

    protected function setItem($key, Item $item)
    {
        return $this->_values->setVal($key, $item);
    }

    protected function getItem($key)
    {
        return $this->_values->getVal($key);
    }

    protected function delItem($key)
    {
        return $this->_values->delVal($key);
    }

    protected function createItem($value)
    {
        if (is_array($value))
        {
            $item = new ItemAggregate($value);
        }else{
            $item = new ItemSingle($value);
        }
        return $item;
    }

    /**
     * @NoHelp
     */
    public function &offsetGet($key)
    {
        return $this->read[$key];
    }

    /**
     * @NoHelp
     */
    public function offsetSet($key, $value)
    {
        $this->write($key, $value);
    }

    /**
     * @NoHelp
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * @NoHelp
     */
    public function offsetUnset($key)
    {
       $this->del($key);
    }

    /**
     * 配列にする
     *
     * @return array
     */
    public function toArray()
    {
        $ret = [];

        foreach($this->_values as $k => $v)
        {
            if ($v instanceof ItemAggregate)
            {
                $ret[$k] = $v->toArray();
            }else{
                $ret[$k] = $v->read();
            }
        }
        return $ret;
    }
}

