<?php
namespace Nora\Data\Cache;

use Nora\Data\DataBase;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * キャッシュ
 */
class Facade extends Client
{
    private $_db;

    public function __construct( )
    {
    }

    public function setDBHandler(DataBase\Facade $db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * キャッシュを使用開始する
     */
    public function connect($spec)
    {
        $storage = $this->injection(['KVS', function ($kvs) use ($spec) {
            return $kvs->getStorage($spec);
        }]);

        parent::__construct($storage);
    }

    /**
     * ハンドラを増やす
     */
    public function getHandler($name)
    {
        return new Client($this, $name);
    }

    public function __get($name)
    {
        return $this->getHandler($name);
    }
}
