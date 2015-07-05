<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Data\Base;

use Nora\Base\Data\DataSource;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora;

/**
 * データのハンドリング
 */
class DataHandler extends Component\Component
{
    private $_DB;
    private $_storage_name;
    private $_table_name;

    protected function initComponentImpl( )
    {
        $this->injection([
            'Database', 
            function ($DB) {
                $this->_DB = $DB;
            }
        ]);
    }

    /**
     * @param string $name databaseコンポーネントに登録されている名前
     */
    public function setStorage($name)
    {
        $this->_storage_name = $name;
        return $this;
    }

    /**
     * @param string $name テーブル名
     */
    public function setTable($name)
    {
        $this->_table_name = $name;
        return $this;
    }

    /**
     * @param string $name テーブル名
     */
    public function getDataClassName( )
    {
        return __namespace__.'\\Data';
    }

    /**
     * データソースを取得
     *
     * @param string $name テーブル名
     */
    public function getDataSource( )
    {
        return DataSource\DataSource::createDataSource($this->_DB->getConnection(
            $this->_storage_name
        ));
    }


    /**
     * データの数を数える
     */
    public function count($query = [])
    {
        return $this->getDataSource()->count($this->_table_name, $query);
    }

    /**
     * データを一件取得する
     */
    public function get($query)
    {
        return $this->getDataSource()->get($this->_table_name, $query);
    }

    /**
     * データを作成する
     */
    public function insert($data)
    {
        return $this->getDataSource()->insert($this->_table_name, $data);
    }

    /**
     * データを検索する
     */
    public function find($query = [], $options =[])
    {
        return $this->getDataSource()->find($this->_table_name, $query, $options);
    }

    /**
     * データを更新する
     */
    public function update($query, $datas)
    {
        return $this->getDataSource()->update($this->_table_name, $query, $datas);
    }

    /**
     * データを削除する
     */
    public function remove($datas)
    {
        return $this->getDataSource()->remove($this->_table_name, $datas);
    }

    /**
     * データを集計する
     */
    public function aggregate($query)
    {
        return $this->getDataSource()->aggregate($this->_table_name, $query);
    }
}
