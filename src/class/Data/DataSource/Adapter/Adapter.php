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
abstract class Adapter implements AdapterIF
{
    use AttrTrait;

    private $_con;

    static public function createAdapter(DataBase\Client\Base\Facade $con, DataSource\Spec $spec)
    {
        if ($con instanceof DataBase\Client\Mongo\Facade)
        {
            return new Mongo($con, $spec);
        }
    }

    private function __construct($con, $spec)
    {
        $this->_con = $con;
        $this->initAdapter($spec);
    }

        
    protected function con( )
    {
        return $this->_con;
    }

    protected function initAdapter($spec)
    {
        $this->setAttr($spec->getAttrs());
    }
    
}
