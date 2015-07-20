<?php
namespace Nora\Data\DataSource\Handler;

use Nora\Data\DataSource\Adapter;
use Nora\Data\DataSource\Cursor;
use Nora\Data\DataBase;
use Nora\Data\DataSource;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * データソースハンドラ
 */
class Handler implements Adapter\AdapterIF
{
    use Adapter\AttrTrait;

    private $_adapter;


    static public function createHandler(DataBase\Client\Base\Facade $con, DataSource\Spec $spec)
    {
        $adapter = Adapter\Adapter::createAdapter($con, $spec);
        return new Handler($adapter);
    }

    public function __construct(Adapter\AdapterIF $adapter)
    {
        $this->_adapter = $adapter;
        $this->setAttr($adapter->getAttrs());
    }

    public function adapter()
    {
        return $this->_adapter;
    }

    public function count($query = [])
    {
        return $this->adapter()->count($query);
    }

    public function findOne($query = [])
    {
        return $this->adapter()->findOne($query);
    }

    /**
     * カーソルが第一引数へ指定された時のみ
     * 本当に検索をする
     * それ以外はカーソルを返す
     */
    public function find($query = [], $options = [])
    {
        if ($query instanceof Cursor\Cursor)
        {
            return $this->adapter()->find($query);

        }else{
            return new Cursor\Cursor($this, $query, $options);
        }
    }

    public function insert($datas)
    {
        return $this->adapter()->insert($datas);
    }

    public function update($query, $datas)
    {
        $this->adapter()->update($query, $datas);
    }


    public function delete($query)
    {
        return $this->adapter()->delete($query);
    }

    public function get($id)
    {
        return $this->findOne([
            $this->adapter()->getAttr('pkey', 'id') => $id
        ]);
    }
}
