<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Hash;

use Nora\Exception\InvalidArgs;

/**
 * ハッシュクラス
 */
class Hash implements HashIF
{
    const OPT_IGNORE_CASE = 1;
    const OPT_ALLOW_UNDEFINED_KEY_SET = 2;
    const OPT_ALLOW_UNDEFINED_KEY_GET = 4;
    const OPT_ALLOW_UNDEFINED_KEY = 6;
    const OPT_DEFAULT = 0;
    const OPT_FULL = 7;
    const OPT_SECURE = self::OPT_FULL ^ self::OPT_ALLOW_UNDEFINED_KEY_GET;
    const OPT_ALLOW_ALL = self::OPT_ALLOW_UNDEFINED_KEY_SET | self::OPT_ALLOW_UNDEFINED_KEY_GET;

    private $_array = [];
    private $_handlers = [];
    private $_option = self::OPT_DEFAULT;
    private $_readonly_keys = [];
    private $_no_overwrite = [];
    private $_original_keymap = [];

    static public function newHash($default = [], $option = 0)
    {
        $hash = new Hash();
        $hash->_option = $option;
        $hash->initValues($default);
        return $hash;
    }

    // For Countable {{{
    
    public function count( )
    {
        return count($this->_array);
    }

    // }}}

    protected function set_hash_option($option)
    {
        $this->_option = $option;
    }

    protected function set_hash_readonly_keys($keys)
    {
        foreach($keys as $k)
        {
            $this->_readonly_keys[$k] = true;
        }
    }

    /**
     * 上書き禁止にする
     */
    protected function set_hash_no_overwrite($keys)
    {
        foreach($keys as $k)
        {
            $this->_no_overwrite[$k] = true;
        }
    }

    /**
     * セットできるキーかチェックする
     *
     * @param string $key
     * @return bool
     */
    protected function _check_is_allow_set($key)
    {
        if (!$this->hasVal($key) && !$this->isAllowUndefinedKeySet())
        {
            return false;
        }


        if (in_array($key, array_keys($this->_readonly_keys)) && $this->_readonly_keys[$key] === true)
        {
            return false;
        }

        return true;
    }

    /**
     * ハッシュ値をイニシャライズする
     *
     * @param array $values
     * @return Hash
     */
    public function initValues($values)
    {
        if ($values === null || $values === false) $values = [];

        if ( !is_array($values) )
        {
            throw new InvalidArgs(__class__, __function__, func_get_args());
        }

        foreach($values as $k=>$v)
        {
            $this->_setVal($k, $v);
        }
        return $this;
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function &getVal($key, $default = null)
    {
        if (!$this->hasVal($key))
        {
            if (!$this->isAllowUndefinedKeyGet())
            {
                throw new Exception\HashKeyNotExists($this, $key);
            }
            return $default;
        }
        if ($this->isIgnoreCase()) $key = strtolower($key);
        return $this->_on_get_val($key, $this->_array[$key]);
    }

    public function registerGetValHandler($cb)
    {
        if (!is_callable($cb)) throw new Exception\InvalidCallback($this);
        $old = $this->_handlers['GetVal'];
        $this->_handlers['GetVal'] = $cb;
        return $old;
    }

    public function registerSetValHandler($cb)
    {
        if (!is_callable($cb)) throw new Exception\InvalidCallback($this);
        $old = $this->_handlers['SetVal'];
        $this->_handlers['SetVal'] = $cb;
        return $old;
    }

    /**
     * 値が書き換わる直前に呼ばれる
     *
     * @param string $key
     * @param string $value
     */
    protected function _on_set_val($key, $value)
    {
        if (isset($this->_handlers['SetVal']))
        {
            return call_user_func($this->_handlers['SetVal'], $key, $value, $this);
        }
        return $value;
    }

    /**
     * 値を取得する時に呼ばれる
     *
     * @param string $key
     * @param string $value
     */
    protected function &_on_get_val($key, &$value)
    {
        if (isset($this->_handlers['GetVal']))
        {
            return call_user_func($this->_handlers['GetVal'], $key, $value, $this);
        }
        return $value;
    }


    /**
     * チェック付きのセット
     *
     * @param string $key
     * @param string $value
     * @return Hash
     */
    public function setVal($key, $value = null)
    {
        if (is_array($key) || $key instanceof Hash)
        {
            foreach($key as $k=>$v) $this->setVal($k, $v);
            return $this;
        }


        if (!$this->_check_is_allow_set($key))
        {
            throw new Exception\SetOnNotAllowedKey($this, $key);
        }

        if ($this->hasVal($key) && in_array($key, array_keys($this->_no_overwrite)) && $this->_no_overwrite[$key] === true)
        {
            throw new Exception\OverwriteOnNotAllowedKey($this, $key);
        }

        return $this->_setVal($key, $value);
    }

    /**
     * チェック無しのセット
     *
     * @param string $key
     * @param string $value
     * @return Hash
     */
    protected function _setVal($key, $value)
    {
        $this->_original_keymap[strtolower($key)] = $key;

        if ($this->isIgnoreCase()) $key = strtolower($key);
        $this->_array[$key] = $this->_on_set_val($key, $value);
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasVal($key, $true = true, $false = false)
    {
        if ($this->isIgnoreCase()) $key = strtolower($key);

        if (is_callable($true))
        {
            if ($this->hasVal($key)) {
                $true($this->getVal($key));
            }
            return $this;
        }


        if(array_key_exists($key, $this->_array))
        {
            return $true;
        }
        return $false;
    }

    /**
     * @param string $key
     * @return void
     */
    public function delVal($key)
    {
        if ($this->isIgnoreCase()) $key = strtolower($key);
        unset($this->_array[$key]);
    }


    # 拡張機能 {{{

    /**
     * 一括設定
     *
     * @param array
     * @return Hash
     */
    public function setVals($values)
    {
        foreach($values as $k=>$v)
        {
            $this->setVal($k, $v);
        }
        return $this;
    }

    /**
     * 一括取得
     *
     * @param array
     * @return array
     */
    public function getVals($map)
    {
        $result = [];
        foreach($this->getKeys() as $k)
        {
            if (is_array($map)) {
                if (!in_array($k, $map)) continue;
            }

            $result[$this->_original_keymap[$k]] = $this->getVal($k);
        }
        return $result;
    }

    /**
     * キーを取得
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->_array);
    }



    /**
     * イテレーション
     *
     * @param array map = ['a','b']
     * @param array callback 
     * @return Hash
     */
    public function each($map, $cb = null) 
    {
        if ($cb === null) {
            $cb = $map;
            $map = null;
        }

        if (!is_callable($cb)) throw new Exception\InvalidCallback($this);

        foreach($this->getVals($map) as $k=>$v)
        {
            $cb($v, $k);
        }
        return $this;
    }

    /**
     * フィルタリング
     *
     * @param array $cb callback
     * @return Hash
     */
    public function filter($cb)
    {
        if (!is_callable($cb)) throw new Exception\InvalidCallback($this);

        foreach($this->getVals($map) as $k=>$v)
        {
            if(false == $cb($v, $k))
            {
                $this->delVal($k);
            }
        }
        return $this;
    }

    public function dump( )
    {
        var_Dump($this->__debugInfo());
    }

    public function toArray( )
    {
        return iterator_to_array($this);
    }

    public function reverse( )
    {
        $hash = clone $this;
        $hash->_array = array_reverse($this->_array);
        return $hash;
    }

    # }}}


    # for Magic Methods {{{

    public function __isset($key)
    {
        return $this->hasVal($key);
    }

    public function &__get($key)
    {
        return $this->getVal($key);
    }

    public function __set($key, $value)
    {
        return $this->setVal($key, $value);
    }

    public function __debugInfo( )
    {
        return [
            'option' => $this->_option,
            'data' => $this->_array,
            'readonly' => $this->_readonly_keys
        ];
    }

    # }}}

    # for IteratorAggregate {{{
    public function getIterator( )
    {
        foreach($this->getKeys() as $k)
        {
            yield $this->_original_keymap[$k] => $this->getVal($k);
        }
    }

    # }}

    public function inArray($val)
    {
        foreach($this as $k=>$v)
        {
            if ($v == $val) return true;
        }
        return false;
    }


    # Option:Checker {{{

    public function isIgnoreCase( )
    {
        return $this->_option & self::OPT_IGNORE_CASE ? true: false;
    }

    public function isAllowUndefinedKeySet( )
    {
        return $this->_option & self::OPT_ALLOW_UNDEFINED_KEY_SET ? true: false;
    }

    public function isAllowUndefinedKeyGet( )
    {
        return $this->_option & self::OPT_ALLOW_UNDEFINED_KEY_GET ? true: false;
    }

    # }}}

    # for ArrayAccess {{{
    #
    public function &offsetGet($key)
    {
        return $this->getVal($key);
    }

    public function offsetSet($key, $value)
    {
        return $this->setVal($key, $value);
    }

    public function offsetExists($key)
    {
        return $this->hasVal($key);
    }
    public function offsetUnset($key)
    {
        return $this->delVal($key);
    }

    # }}}
    
}
