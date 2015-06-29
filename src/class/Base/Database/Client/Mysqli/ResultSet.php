<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Database\Client\Mysqli;

use Nora\Base\Database\Client\Base;
use Nora\Base\Hash;
use Nora;

use Mysqli;

/**
 * Mysqli用のResultSet
 *
 */
class ResultSet
{
    private $_facade;
    private $_res;

    public function __construct($facade, $res)
    {
        $this->_facade = $facade;
        $this->_res = $res;
    }

    public function fetch( )
    {
        return $this->_facade->fetch($this->_res);
    }

    public function each($cb)
    {
        while($raw = $this->fetch())
        {
            $cb($raw);
        }
        return $this;
    }
}
