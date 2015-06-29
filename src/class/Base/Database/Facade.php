<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Database;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora;

/**
 * データベース
 *
 */
class Facade extends Component\Component
{
    private $_connection;
    private $_cache;

    protected function initComponentImpl( )
    {
        $this->_connection = Hash\Hash::newHash([], Hash\Hash::OPT_IGNORE_CASE | Hash\Hash::OPT_ALLOW_UNDEFINED_KEY_SET);
        $this->_cache = Hash\Hash::newHash([], Hash\Hash::OPT_IGNORE_CASE | Hash\Hash::OPT_ALLOW_UNDEFINED_KEY_SET);
    }

    public function setConnection($name, $string)
    {
        return $this->_connection->setVal($name, $string);
    }

    public function getConnection($name)
    {
        try
        {
            $string = $this->_connection->getVal($name);
            if (!$this->_cache->hasVal($string))
            {
                $this->_cache->setVal($string, $this->createConnection($string));
            }
            return $this->_cache->getVal($string);

        } catch (Hash\Exception\HashKeyNotExists $e) {
            throw new Exception\NoConnection($this, $name);
        }
    }

    public function hasConnection($name)
    {
        return $this->_connection->hasVal($name);
    }

    public function createConnection($string)
    {
        $spec = new Util\SpecLine($string);
        $class = sprintf(__namespace__.'\\Client\\%s\\Facade', ucfirst($spec->getScheme()));
        return $class::make($this, $spec);
    }

    public function __invoke($name)
    {
        return 
            $this->getConnection($name);
    }
}
