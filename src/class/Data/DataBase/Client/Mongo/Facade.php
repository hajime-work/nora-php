<?php
namespace Nora\Data\DataBase\Client\Mongo;

use Nora\Data\DataBase\Client\Base;
use Nora\Base\Hash;
use Nora;

use MongoClient;

/**
 * Mongodb用のFacade
 *
 */
class Facade extends Base\Facade
{
    private $_db;

    protected function initClient($spec)
    {
        $con = 'mongodb://'.$spec->host;
        if($spec->hasAttr('replicaSet'))
        {
            $con .= '/?replicaSet='.$spec->getAttr('replicaSet');
        }

        $client = new MongoClient($con);

        $this->setConnection($client);
        $this->_db = $client->{$spec->field};
    }

    public function status ( )
    {
        var_Dump($this->con()->getHosts());
        var_Dump($this->con()->listDBs());
        echo "Collections\n";
        foreach($this->_db->listCollections() as $col)
        {
            var_Dump($col->getName());
        }
    }

    public function getCollection($name)
    {
        return $this->_db->{$name};
    }
}
