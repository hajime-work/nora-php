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

    public function find($name, $query, $options)
    {
        $cur = $this->ds()->getCollection($name)->find($query);

        if (isset($options['limit']))
        {
            $cur->limit($options['limit']);
        }

        if (isset($options['offset']))
        {
            $cur->skip($options['offset']);
        }

        $ret = [];
        foreach($cur as $v)
        {
            $ret[] = $v;
        }
        return $ret;
    }

    public function aggregate($name, $query)
    {
        return $this->ds()->getCollection($name)->aggregate($query);
    }

}
