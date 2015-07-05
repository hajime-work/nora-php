<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Model\Base;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Model;
use Nora\DataSource;
use Nora;

/**
 * モデルハンドラ
 */
class ModelHandler
{
    private $_spec;
    private $_ds;

    public function __construct(Model\Facade $facade, DataSource\Handler\Handler $handler)
    {
        $this->_ds = $handler;
    }

    public function initComponentImpl( )
    {
    }

    /**
     * 件数を取得する
     *
     * @return int
     */
    public function count ($query = [])
    {
        return $this->_ds->count($query);
    }

    /**
     * 検索CURを作成する
     */
    public function find($query = [])
    {
        return new Cursol($this, $query);
    }

    /**
     * 検索を実行する
     */
    public function findReal($query = [], $options = [])
    {
        return $this->_ds->find($query, $options);
    }

    public function onGetFilter($datas)
    {
        $model = $this->createModel($datas);
        $model->clearChanged();
        return $model;
    }

    public function getModelClass( )
    {
        return __namespace__.'\\Model';
    }

    public function createModel($datas)
    {
        $class = $this->getModelClass();
        return new $class($datas, $this);
    }

    public function saveModel(Nora\Model\ModelIF $model)
    {
        // 変更があったか
        if (!$model->isChanged())
        {
            // なければスルー
            return 0;
        }

        // 変更があったら
        var_dump($model->getChangedVars());

        // 個体識別情報
        var_dump($model->getIdentity());
    }
}
