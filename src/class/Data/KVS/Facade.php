<?php
namespace Nora\Data\KVS;

use Nora\Data\DataBase;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * Key Value Storage
 */
class Facade extends Component\Component
{
    private $_db;

    public function __construct( )
    {
    }

    protected function initComponentImpl()
    {
    }

    /**
     * DBハンドラを設定する
     */
    public function setDBHandler($DB)
    {
        $this->_db = $DB;
        return $this;
    }

    /**
     * KVSストレージを取得する
     */
    public function getStorage($spec)
    {
        $con = $this->_db->connect($spec);

        // タイプ別にする
        if ($con instanceof DataBase\Client\Base\Facade)
        {
            $con_class = get_class($con);
            $type = basename(dirname(str_replace('\\', '/', $con_class)));

            $class = __namespace__.'\\Storage\\'.$type.'Storage';

            $storage = $class::createComponent($this->scope()->newScope($type.'Storage'));
            $storage->initStorage($con, $spec);
            return $storage;
        }

        throw new Exception(Nora::message('ストレージ %s がみつかりません', $spec));
    }
}
