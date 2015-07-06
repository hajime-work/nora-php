<?php
namespace Nora\Data\Database\Client\Mysqli;

use Nora\Base\Database\Client\Base;
use Nora\Base\Hash;
use Nora;

use Mysqli;

/**
 * ResultSet
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
