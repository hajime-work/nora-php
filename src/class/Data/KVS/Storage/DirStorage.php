<?php
namespace Nora\Data\KVS\Storage;

use Nora\Data\KVS;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * Key Value Storage
 */
class DirStorage extends Storage
{
    public function initStorage($con, $spec)
    {
        $this->_con = $con;
        $this->_spec = $spec;
    }

    public function get($key)
    {
        return $this->_con->get($key);
    }

    public function set($key, $value)
    {
        $this->_con->set($key, $value);
    }

    public function has($key)
    {
        return $this->_con->has($key);
    }

    public function delete($key)
    {
        return $this->_con->delete($key);
    }

    public function swipe($time = 3600)
    {
        return $this->_con->swipe($time);
    }

    public function ensure($key)
    {
        $this->_con->ensure($key);
    }
}
