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

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Base\Database\Client;
use Nora;

/**
 * データ:ソースラッパー:Mongo
 *
 */
class Mongo extends DataSource
{
    public function count($name, $query)
    {
        return $this->ds()->getCollection($name)->count($query);
    }

    public function get($name, $query)
    {
        return $this->ds()->getCollection($name)->findOne($query);
    }

    public function insert($name, $datas)
    {
        return $this->ds()->getCollection($name)->insert($datas);
    }
}
