<?php
namespace Nora\Data\DataSource;

use Nora\Data\DataBase;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora\Util\Util;
use Nora;

/**
 * データソースハンドラ
 */
class Facade
{
    private $_db;
    private $_loader;
    private $_specs;

    public function __construct( )
    {
        $this->_specs = Nora::Hash();
        $this->_loader = Util::instanceLoader(function($key) {
            return $this->create(
                $this->_specs[$key]
            );
        });
    }

    public function setDBHandler(DataBase\Facade $db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * データソースを作成する
     */
    public function create($spec, $options= [])
    {
        if (!($spec instanceof Spec))
        {
            $spec = new Spec($spec);
        }

        // DBとの接続を確保
        $con = $this->_db->getConnection($spec->database);
        $spec->setAttr($options);

        // ハンドラを作成する
        return Handler\Handler::createHandler($con, $spec);
    }

    public function setDataSource($key, $spec = null)
    {
        if (is_array($key))
        {
            foreach($key as $k=>$v) $this->setDataSource($k, $v);
            return $this;
        }

        if (!($spec instanceof Spec))
        {
            $spec = new Spec($spec);
        }

        $this->_specs[$key] = $spec;
        return $this;
    }

    public function getDataSource($key)
    {
        return $this->_loader->get($key);
    }

    public function __invoke($key)
    {
        return $this->getDataSource($key);
    }


}
