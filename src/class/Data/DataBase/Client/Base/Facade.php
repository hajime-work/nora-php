<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Data\DataBase\Client\Base;

use Nora\Data\DataBase\Connection\Spec;
use Nora;


/**
 * Mysqli用のFacade
 */
abstract class Facade
{
    private $_real_connection;

    public function __construct (Spec $spec)
    {
        $this->initClient($spec);
    }

    protected function setConnection($con)
    {
        $this->_real_connection = $con;
    }

    protected function con( )
    {
        return $this->_real_connection;
    }

    abstract protected function initClient($spec);

    public function getRealConnection( )
    {
        return $this->con();
    }
}
