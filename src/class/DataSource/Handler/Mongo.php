<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\DataSource\Handler;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Base\Database\Client;
use Nora;

/**
 * データソースハンドラー
 */
class Mongo extends Handler
{
    private $_end_point;

    protected function initHandler()
    {
        $this->_end_point = $this->_ds->getCollection($this->_spec->host());
    }

    /**
     * データ件数を返す
     *
     * @param array $query
     * @return int
     */
    public function count($query = [])
    {
        return $this->_end_point->count($query);
    }

    public function get($name, $query)
    {
        return $this->ds()->getCollection($name)->findOne($query);
    }

    public function insert($name, $datas)
    {
        return $this->ds()->getCollection($name)->insert($datas);
    }

    public function update($name, $query, $datas)
    {
        die(__file__.':'.__line__);
        return $this->ds()->getCollection($name)->update($query, $datas);
    }

    public function remove($name, $datas)
    {
        return $this->ds()->getCollection($name)->remove($datas);
    }

    /**
     * データを検索する
     *
     * @param string $query
     * @param array $options
     * @return array
     */
    public function find($query, $options)
    {
        $cur = $this->_end_point->find($query);

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

    /**
     * 集計する
     *
     * @param string $name
     * @param array $query
     */
    public function aggregate($name, $query)
    {
        return $this->ds()->getCollection($name)->aggregate($query);
    }

}
