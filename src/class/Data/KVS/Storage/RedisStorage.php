<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Data\KVS\Storage;


use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * RedisStorage
 */
class RedisStorage  extends Storage
{
    public function initStorage($con, $spec)
    {
        $this->_con = $con;
        $this->_spec = $spec;
    }


    public function set($name, $value)
    {
        $this->_con->set($name, $value);
        return $this;
    }

    public function delete($name)
    {
        $this->_con->delete($name);
    }

    public function get($name)
    {
        return $this->_con->get($name);
    }

    public function has($name)
    {
        if (!$this->_con->has($name))
        {
            return false;
        }

        return true;
    }

    public function swipe($time = 3600)
    {
        foreach($this->_con->getKeys('*') as $k)
        {
            $k = str_replace($this->_prefix, '', $k);
            $data = $this->_con->get($k);
            if (time() - $data['created_at'] > $time)
            {
                $this->delete($k);
            }
        }
    }

    public function ensure($key)
    {
        $this->_con->ensure($key);
    }
}
