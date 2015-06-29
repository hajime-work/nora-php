<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Data\DataSource;

use Nora\Base\Data\Exception;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Base\Database\Client;
use Nora;

/**
 * データ:ソースラッパー
 *
 */
class DataSource 
{
    public function __construct(Client\Base\Facade $client)
    {
        $this->_client = $client;
    }

    public function ds()
    {
        return $this->_client;
    }

    static public function createDataSource($ds)
    {
        if ($ds instanceof Client\Mongo\Facade)
        {
            return new Mongo($ds);
        }

        throw new Exception\DoseNotSupport($ds);
    }
}
