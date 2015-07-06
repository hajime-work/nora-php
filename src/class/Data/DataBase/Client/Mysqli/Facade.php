<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Data\DataBase\Client\Mysqli;

use Nora\Data\DataBase\Client\Base;
use Nora;

use Mysqli;

/**
 * Mysqli用のFacade
 */
class Facade extends Base\Facade
{
    protected function initClient($spec)
    {
        // コネクト
        $this->setConnection(
            new mysqli(
                $spec->host,
                $spec->getAttr('user'),
                $spec->getAttr('pass'),
                $spec->field
            )
        );
    }

    public function fetch($res)
    {
        return $res->fetch_assoc( );
    }

    public function query($sql)
    {
        if(false === $res = $this->con( )->query($sql))
        {
            // Query Failed
            throw new Exception\QueryFailed($this, $res, $sql);
        }

        return new ResultSet($this, $res);
    }

    public function getError( )
    {
        return $this->con()->error;
    }
}
