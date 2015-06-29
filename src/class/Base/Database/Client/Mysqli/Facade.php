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
 * Mysqli用のFacade
 *
 */
class Facade extends Base\Facade
{
    private $_con;

    public function initClient($spec)
    {
        $this->_con = new mysqli($spec['host'], $spec['user'], $spec['pass'], ltrim($spec['path'],'/'));
    }

    public function fetch($res)
    {
        return $res->fetch_assoc( );
    }

    public function query($sql)
    {
        if(false === $res = $this->_con->query($sql))
        {
            // Query Failed
            throw new Exception\QueryFailed($this, $res, $sql);
        }

        return new ResultSet($this, $res);
    }

    public function getError( )
    {
        return $this->_con->error;
    }
}
