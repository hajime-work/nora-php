<?php
namespace Nora\Data\Model\Base;

use Nora\Data\DataBase;
use Nora\Data\DataSource;
use Nora\Base\Component;
use Nora\Base\Hash\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * モデル
 */
class Model extends Hash
{
    private $_handler;
    private $_origin;
    private $_is_new = true;

    public function __construct(Handler $handler, $datas = [])
    {
        $this->set_hash_option(Hash::OPT_IGNORE_CASE|Hash::OPT_ALLOW_UNDEFINED_KEY_SET);
        $this->initValues($datas);
        $this->_handler = $handler;
    }

    public function getIdentity(  )
    {
        return [
            $this->handler()->getAttr('pkey', 'id') => $this[$this->handler()->getAttr('pkey', 'id')]
        ];
    }

    public function isNew($res = null)
    {
        if ($res === null)
        {
            return $this->_is_new;
        }
        $this->_is_new = $res;
    }

    /**
     * 変更をフラグを消す
     */
    public function initChanged()
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

    public function handler()
    {
        return $this->_handler;
    }

    public function save( )
    {
        if ($this->isNew())
        {
            $this->handler()->insertModel($this);
        }else{
            $this->handler()->updateModel($this);
        }

        return $this;
    }
}
