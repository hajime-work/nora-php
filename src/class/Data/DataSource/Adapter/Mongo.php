<?php
namespace Nora\Data\DataSource\Adapter;

use Nora\Base\Component;
use Nora\Base\Hash\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

use Nora\Data\DataSource\Cursor\Cursor;

/**
 * データソースアダプター: Mongo
 */
class Mongo extends Adapter
{
    protected $_ds;

    protected function initAdapter($spec)
    {
        parent::initAdapter($spec);

        // テーブルを取得する
        $this->_ds = $this->con()->getCollection($spec->field);
    }

    public function count($query = [])
    {
        return $this->_ds->count($query);
    }

    public function findOne($query = [])
    {
        return $this->_ds->findOne($query);
    }

    public function drop( )
    {
        $this->_ds->drop();
    }

    public function insert($datas)
    {
        $this->_ds->insert($datas);
        return $this;
    }

    public function update($query, $datas)
    {
        $this->_ds->update($query, [
            '$set' => $datas
        ]);
        return $this;
    }

    public function delete($query)
    {
        return $this->_ds->remove($query);
    }

    public function find($query, $options = [])
    {
        if ($query instanceof Cursor)
        {
            return $this->find($query->getQuery(), $query->getOptions());
        }

        $cur = $this->_ds->find($query);

        if (!($options instanceof Hash))
        {
            $options = Nora::Hash($options);
        }


        $options->hasVal('limit', function($v) use ($cur) {
            $cur->limit($v);
        })->hasVal('offset', function($v) use ($cur) {
            $cur->skip($v);
        })->hasVal('order', function($v) use ($cur) {
            $cur->sort($v);
        });;

        return $cur;
    }


    // Mongo固有の処理

    public function createIndex($key, $options)
    {
        $this->_ds->createIndex($key, $options);
        return $this;
    }

    public function aggregate($query)
    {
        return $this->_ds->aggregate($query);
    }

}
