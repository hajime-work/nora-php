<?php
namespace Nora\Data\Cache\Adapter;

use Nora\Data\DataBase\Client\Base\Facade as DBCon;
use Nora\Data\Cache\Spec;
/**
 * キャッシュアダプター
 */
abstract class Base implements AdapterIF
{
    protected $_con;
    protected $_spec;

    public function __construct(DBCon $con, Spec $spec)
    {
        $this->_con = $con;
        $this->_spec = $spec;

        $this->initCacheAdapter( );
    }
}
