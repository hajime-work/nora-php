<?php
namespace Nora\Data\Model;

use Nora\Data\DataBase;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora\Util\Util;
use Nora;

/**
 * モデルハンドラ
 */
class Facade
{
    private $_ds;

    public function setDataSourceHandler($DS)
    {
        $this->_ds = $DS;
        $this->_loader = Util::InstanceLoader(function($key) {
            return $this->createHandler($key);
        });
    }

    public function getHandler($name)
    {
        return $this->_loader->get($name);
    }

    public function createHandler($name)
    {
        $ds = $this->_ds->getDataSource($name);

        return new Base\Handler($ds);
    }
}
