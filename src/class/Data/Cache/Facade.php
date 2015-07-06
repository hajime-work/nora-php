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
        if (!($spec instanceof Spec))
        {
            $spec = new Spec($spec);
        }

        // DBとの接続を確保
        $con = $this->_db->getConnection($spec->database);

        // DBのタイプ別にハンドラを立ち上げる
        if ($con instanceof DataBase\Client\Redis\Facade)
        {
            $engine = new Adapter\Redis( $con, $spec );
        }

        parent::__construct($engine, $spec->get('field', null));
    }

    /**
     * ハンドラを増やす
     */
    public function getHandler($name)
    {
        return new Client($this, $name);
    }
}
