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

use Nora\DataSource;
use Nora\Base\Data\Exception;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Base\Database\Client;
use Nora;

abstract class Handler
{
    protected $_ds;
    protected $_spec;
    protected $_facade;

    private function __construct($ds, $spec, $facade)
    {
        $this->_ds = $ds;
        $this->_spec = $spec;
        $this->_facade = $facade;

        $this->initHandler ();
    }

    abstract protected function initHandler();

    static public function createHandler(Client\Base\Facade $ds, $spec, DataSource\Facade $facade)
    {
        // データソースのクラスによってクラスを変える
        if ($ds instanceof Client\Mongo\Facade)
        {
            return new Mongo($ds, $spec, $facade);
        }

        throw new Exception\DoseNotSupport($ds);
    }

    public function __construct(DataBase\Client\Base\Facade $con, $spec)
    {
        $this->_attrs = Nora::Hash($spec->getAttrs());

        // 目当てのテーブル名
        $this->_table = $spec->field;

        $this->initHandler()
    }

    public function getPkey( )
    {
        return $this->_attrs->getVal('pkey', 'id');
    }

    public function get ($value)
    {
        return $this->findOne([
            $this->getPkey() => $value
        ]);
    }
}
