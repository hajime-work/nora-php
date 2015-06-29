<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Cache\Handler;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * キャッシュハンドラ
 */
class Redis extends Base
{
    private $_handler;
    private $_data;

    protected function initCacheHandler(SpecLine $spec)
    {
        $this->injection([
            'Database', function ($DB) use ($spec) {

                // データハンドラを取得する
                $this->_data = $DB($spec->host());
            }
        ]);
    }

    public function set($name, $value, $options = [])
    {
        $this->logDebug("[Cache] set $name");
        $this->_data->set($name, [
            'value' => $value,
            'options' => [
                'created_at' => time()
            ] + $options
        ]);
        return $this;
    }

    public function get($name)
    {
        $this->logDebug("[Cache] get $name");

        if ($this->_data->has($name))
        {
            $data = $this->_data->get($name);
            return $data['value'];
        }
        return false;
    }

    public function has($name, $expire = -1, $create_after = -1)
    {
        if (!$this->_data->has($name))
        {
            return false;
        }

        $v = $this->_data->get($name);

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
