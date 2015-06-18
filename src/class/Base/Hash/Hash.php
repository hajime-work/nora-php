<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Hash;

/**
 * ハッシュクラス
 */
class Hash implements HashIF
{
    const OPT_IGNORE_CASE = 1;
    const OPT_ALLOW_UNDEFINED_KEY_SET = 2;
    const OPT_ALLOW_UNDEFINED_KEY_GET = 4;
    const OPT_DEFAULT = 0;
    const OPT_FULL = 7;
    const OPT_SECURE = self::OPT_FULL ^ self::OPT_ALLOW_UNDEFINED_KEY_SET;
    const OPT_ALLOW_ALL = self::OPT_ALLOW_UNDEFINED_KEY_SET | self::OPT_ALLOW_UNDEFINED_KEY_GET;

    private $_array = [];
    private $_handlers = [];
    private $_option = self::OPT_DEFAULT;
    private $_readonly_keys = [];

    static public function newHash($default = [], $option = 0)
    {
        $hash = new Hash();
        $hash->_option = $option;
        $hash->initValues($default);
        return $hash;
    }

    protected function set_hash_option($option)
    {
        $this->_option = $option;
    }

    protected function set_hash_readonly_keys($keys)
    {
        $this->_readonly_keys = $keys;
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

        if (in_array($key, $this->_readonly_keys))
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
    protected function _on_get_val($key, $value)
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
    public function setVal($key, $value)
    {
        if (!$this->_check_is_allow_set($key))
        {
            throw new Exception\SetOnNotAllowedKey($this, $key);
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
        if ($this->isIgnoreCase()) $key = strtolower($key);
        $this->_array[$key] = $this->_on_set_val($key, $value);
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasVal($key)
    {
        if ($this->isIgnoreCase()) $key = strtolower($key);
        return array_key_exists($key, $this->_array);
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
        foreach($this->getKeys() as $k)
        {
            if (is_array($map)) {
                if (!in_array($k, $map)) continue;
            }

            $result[$k] = $this->getVal($k);
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
            'data' => $this->_array
        ];
    }

    # }}}

    # for IteratorAggregate {{{
    public function getIterator( )
    {
        foreach($this->getKeys() as $k)
        {
            yield $k => $this->getVal($k);
        }
    }

    # }}


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
