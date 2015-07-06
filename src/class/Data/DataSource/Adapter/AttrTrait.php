<?php
namespace Nora\Data\DataSource\Adapter;

use Nora\Data\DataSource;
use Nora\Data\DataBase;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * データソースアダプター
 */
trait AttrTrait
{
    private $_attrs;
    
    public function attrs( )
    {
        if ($this->_attrs == null)
        {
            $this->_attrs = Nora::Hash();
        }
        return $this->_attrs;
    }


    public function getAttrs()
    {
        return $this->attrs();
    }

    public function setAttr($k, $v = null)
    {
        $this->attrs()->setVal($k, $v);
        return $this;
    }

    public function getAttr($k, $v = null)
    {
        return $this->attrs()->getVal($k, $v);
    }

    public function hasAttr($k)
    {
        return $this->attrs()->hasVal($k);
    }
}
