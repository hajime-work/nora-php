<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\DataSource;

use Nora\DataSource\Handler\Handler;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * データソース
 */
class Facade extends Component\Component
{
    private $_data_sources = null;
    private $_cache = null;
    private $_db;

    protected function initComponentImpl( )
    {
        $this->_data_sources = Nora::Hash();
        $this->_cache = Nora::Hash();

        $this->injection([
            'Database',
            function ($db) {
                $this->_db = $db;
            }
        ]);
    }

    /**
     * データソースを登録する
     *
     * @param string $name
     * @param array $spec
     * @return Facade
     */
    public function addSource($name, $spec = [])
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v) $this->addSource($k, $v);
            return $this;
        }

        $this->_data_sources[$name] = $spec;
    }

    /**
     * @param string $name
     */
    public function getDataSource($name)
    {
        if (!$this->_cache->hasVal($name))
        {
            $this->_cache->setVal($name, $this->createDataSource($name));
        }
        return $this->_cache->getVal($name);
    }

    /**
     * @param string $source
     */
    public function createDataSource($name)
    {
        if ($this->_data_sources->hasVal($name))
        {
            $spec = new SpecLine($this->_data_sources->getVal($name)['ds']);

            // データベース名を取り出す
            $con = $this->_db->getConnection($spec->scheme());

            // テーブル名を取得する
            $table = $spec->host();

            $handler = Handler::createHandler($con, $spec, $this);
            return $handler;
        }

        throw Exception\UndefinedDataSource($this, $name);
    }

    /**
     * @param string $name
     */
    public function __invoke($name)
    {
        return $this->getDataSource($name);
    }
}
