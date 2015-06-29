<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Database\Client\Mongo\Util;

use Nora\Base\Component;
use Nora\Base\Database\Client\Mongo\Exception;
use Nora;

use MongoDB;

/**
 * MongodbのCollection
 */
class Collection extends Component\Component
{
    private $_name;
    private $_db;
    private $_map;
    private $_col;

    public function __construct(MongoDB $db, $name)
    {
        $this->_db = $db;
        $this->_name = $name;
    }

    protected function initComponentImpl( )
    {
        $this->injection([
            'Configure',
            function($c) {
                $this->_map = $c('database.mongo.collection', []);
            }
        ]);

        // 設定の中にあるか
        if(isset($this->_map[$this->_name]))
        {
            $this->logDebug(sprintf(
                "Collection Loaded: %s => %s",
                $this->_name,
                $this->_map[$this->_name]
            ));
            $this->_col = $this->_db->{$this->_map[$this->_name]};
        }else{
            throw new Exception\CollectionNotFound($this, $this->_name);
        }
    }

    public function count ($q)
    {
        return $this->_col->count($q);
    }

    public function findOne ($q)
    {
        return $this->_col->findOne($q);
    }

    public function insert($data)
    {
        return $this->_col->insert($data);
    }

    public function find($q)
    {
        return $this->_col->find($q);
    }

    public function aggregate($q)
    {
        return $this->_col->aggregate($q);
    }

    
}
