<?php
namespace Nora\Data\Cache;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * キャッシュ
 */
class Client
{
    private $_prefix = null;
    private $_engine;

    public function __construct($storage, $prefix = null)
    {
        $this->_storage = $storage;
        $this->_prefix = $prefix;
    }

    protected function _name_filter($name)
    {
        if ($this->_prefix === null)
        {
            return $name;
        }
        $name =  $this->_prefix.'_'.$name;

        return $name;
    }

    /**
     * キャッシュを作成
     */
    public function set($name, $value, $options = [])
    {
        $name = $this->_name_filter($name);

        $this->_storage->set($name, [
            'value' => $value,
            'created_at' => time(),
            'options' => $options
        ]);
        return $this;
    }

    /**
     * キャッシュを取得
     */
    public function get($name)
    {
        $name = $this->_name_filter($name);
        return $this->_storage->get($name)['value'];
    }

    /**
     * キャッシュを削除
     */
    public function delete($name)
    {
        $name = $this->_name_filter($name);
        $this->_storage->delete($name);
    }


    /**
     * キャッシュが有効か
     */
    public function has($name, $expire = -1, $create_after = -1)
    {
        $name = $this->_name_filter($name);

        if (!$this->_storage->has($name))
        {
            return false;
        }

        $val = $this->_storage->get($name);

        if ($expire > 0 && (time() - $val['created_at']) > $expire)
        {
            return false;
        }

        if ($create_after > 0 && $val['created_at'] > $create_after)
        {
            return false;
        }

        return true;
    }

    /**
     * UseCache
     *
     * @param string $name
     * @param callable $callback
     * @param int $expire_at
     * @param int $create_after
     */
    public function useCache ($name, $callback, $expire_at = -1, $create_after = -1, &$status = null)
    {
        if($this->has($name, $expire_at, $create_after))
        {
            $status = 'found';
            return $this->get($name);
        }

        $data = $callback($s);
        if ($s === false)
        {
            $status = 'fail';
            return $data;
        }

        $this->set($name, $data);
        $status = 'created';

        return $data;
    }
}
