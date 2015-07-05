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
use Nora\Base\Hash\Hash;
use Nora\DataSource;
use Nora;

/**
 * モデル
 */
class Model extends Hash implements Nora\Model\ModelIF
{
    protected $_handler;
    protected $_origin = [];

    public function __construct($datas, ModelHandler $handler)
    {
        $this->_handler = $handler;

        $this->set_hash_option(Hash::OPT_IGNORE_CASE|Hash::OPT_ALLOW_UNDEFINED_KEY_SET);
        $this->setVal($datas);
        $this->initModel();
    }


    public function initModel( )
    {
    }

    /**
     * プライマリキーを返す
     */
    public function getPrimaryKeys( )
    {
        return [
            'id'
        ];
    }

    /**
     * データを特定できる情報を返す
     */
    public function getIdentity( )
    {
        $ret = [];
        foreach($this->getPrimaryKeys() as $k)
        {
            $ret[$k] = $this[$k];
        }
        return $ret;
    }

    /**
     * 変更通知をクリアする
     */
    public function clearChanged( )
    {
        $this->_origin = $this->toArray();
    }

    /**
     * 変更があるか
     *
     * @return bool
     */
    public function isChanged()
    {
        foreach($this->toArray() as $k=>$v)
        {
            if (!array_key_exists($k, $this->_origin)) return true;
            if ($v !== $this->_origin[$k]) return true;
        }
        return false;
    }

    /**
     * 変更があったデータのみ
     *
     * @return array
     */
    public function getChangedVars()
    {
        $ret = [];

        foreach($this->toArray() as $k=>$v)
        {
            if (!array_key_exists($k, $this->_origin)) {
                $ret[$k] = $v;
                continue;
            }
            if ($v !== $this->_origin[$k]) {
                $ret[$k] = $v;
                continue;
            }
        }
        return $ret;
    }

    protected function _on_set_val($k, $v)
    {
        return $v;
    }

    protected function &_on_get_val($k, $v)
    {
        return $v;
    }

    public function save( )
    {
        $this->_handler->saveModel($this);
    }
}
