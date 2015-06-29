<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Database\Client\Mongo;

use Nora\Base\Database\Client\Base;
use Nora\Base\Hash;
use Nora;

use MongoClient;

/**
 * Mongodb用のFacade
 *
 */
class Facade extends Base\Facade
{
    private $_client;
    private $_db;

    public function initClient($spec)
    {
        if (isset($spec['query']))
        {
            parse_str($spec['query'], $q);
            if (isset($q['replicaSet']))
            {
                $con = 'mongodb://'.$spec['host'];
                if (isset($spec['port'])) $con.=':'.$spec['port'];
                $con .= '/?replicaSet='.$q['replicaSet'];
            }else{
                $con = 'mongodb://'.$spec['host'];
                if (isset($spec['port'])) $con.=':'.$spec['port'];
            }
        }else{
            $con = 'mongodb://'.$spec['host'];
            if (isset($spec['port'])) $con.=':'.$spec['port'];
        }

        $this->logDebug([
            'con' => $con
        ]);

        $this->_client = new MongoClient($con);

        if (isset($spec['path']))
        {
            $default_db = ltrim($spec['path'], '/');
            $this->_db = $this->_client->{$default_db};
        }
    }

    public function status ( )
    {
        var_Dump($this->_client->getHosts());
        var_Dump($this->_client->listDBs());
        echo "Collections\n";
        foreach($this->_db->listCollections() as $col)
        {
            var_Dump($col->getName());
        }
    }

    public function getClient( )
    {
        return $this->_client;
    }

    public function getDB($name = null)
    {
        if ($name === null)
        {
            return $this->_db;
        }
        return $this->getClient()->$name;
    }

    public function getCollection($name)
    {
        $col = new Util\Collection($this->getDB(), $name);
        $col->initComponent($this->scope());
        return $col;
    }
}
