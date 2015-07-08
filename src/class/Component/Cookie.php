<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Component;

use Nora\Base\Component;
use Nora\Base\Hash;

/**
 * クッキー
 */
class Cookie extends Hash\Hash
{
    use Component\Componentable;

    private $_expire   = 0;
    private $_path     = '';
    private $_domain   = '';
    private $_secure   = false;
    private $_httponly = false;

    protected function initComponentImpl( )
    {
        $this->initValues($_COOKIE);
    }

    public function has($key)
    {
        return $this->hasVal($key);
    }

    public function get($key, $value = null)
    {
        return $this->getVal($key);
    }

    public function set($key, $value)
    {
        setcookie($key, $value, $this->_expire, $this->_path, $this->_domain, $this->_secure, $this->_httponly);
    }

    public function dump( )
    {
        var_dump($_COOKIE);
    }
}
