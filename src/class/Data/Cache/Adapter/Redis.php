<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Data\Cache\Adapter;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * キャッシュアダプター
 */
class Redis extends Base
{

    protected function initCacheAdapter( )
    {
    }

    public function set($name, $value, $options = [])
    {
        $this->_con->set($name, [
            'value' => $value,
            'options' => array_merge($options, [
                'created_at' => time()
            ])
        ]);
        return $this;
    }

    public function delete($name)
    {
        $this->_con->delete($name);
    }

    public function get($name)
    {
        if ($this->_con->has($name))
        {
            $data = $this->_con->get($name);
            return $data['value'];
        }
        return false;
    }

    public function has($name, $expire = -1, $create_after = -1)
    {
        if (!$this->_con->has($name))
        {
            return false;
        }

        $v = $this->_con->get($name);

        if ($expire > 0)
        {
            if ($expire < time() - $v['options']['created_at'])
            {
                return false;
            }
        }

        if ($create_after > 0)
        {
            if ($v['options']['created_at'] < $create_after)
            {
                return false;
            }
        }

        return true;
    }
}
