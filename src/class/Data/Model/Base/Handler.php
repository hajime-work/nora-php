<?php
namespace Nora\Data\Model\Base;

use Nora\Data\DataBase;
use Nora\Data\DataSource;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * モデルハンドラ
 */
class Handler extends DataSource\Handler\Handler
{

    /**
     * モデルを作成する
     */
    public function create($datas, $isNew = true)
    {
        $class = __namespace__.'\\Model';
        $model = new $class($this, $datas);

        if ($isNew === false)
        {
            $model->initChanged();
            $model->isNew($isNew);
        }
        return $model;
    }



    /**
     * モデルの挿入
     */
    public function insertModel(Model $model)
    {
        $this->insert($model->toArray());
    }

    /**
     * モデルの更新
     */
    public function updateModel(Model $model)
    {
        // 変更があったものだけ
        $vars  = $model->getChangedVars();
        $ident = $model->getIdentity();

        $this->update($ident, $vars);
    }

    /**
     * モデルの取得
     */
    public function get($id)
    {
        $datas = parent::get($id);
        return $this->create($datas, false);
    }

}
