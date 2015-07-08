<?php
namespace Nora\Data\DataBase;

class FacadeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testMysql()
    {
        $connection = 'mysqli://fspot.mysql.slave/fspot?user=fspot&pass=deganjue';
        $facade = new Facade( );
        $con = $facade->connect($connection);
        $res = $con->query('show tables');
        $res->each(function($v) {
            $this->assertTrue(is_array($v));
        });
    }

    public function testRedis()
    {
        $connection = 'redis://redis01.fuzoku.gallery/fspot-cache';
        $facade = new Facade( );
        $con = $facade->connect($connection);
        $con->set('test', 'abc');
        $this->assertEquals('abc', $con->get('test'));
    }

    public function testMongo()
    {
        $connection = 'mongo://mongodb.fspot/fspot?replicaSet=fspot';
        $facade = new Facade( );
        $con = $facade->connect($connection);
    }

    public function testDir()
    {
        $connection = 'dir:///tmp/hoge';
        $facade = new Facade( );
        $con = $facade->connect($connection);
    }


    public function testMain()
    {
        $facade = new Facade( );
        $facade->setConnection([
            'mysql' => 'mysqli://fspot.mysql.slave/fspot?user=fspot&pass=deganjue',
            'redis' => 'redis://redis01.fuzoku.gallery/fspot-cache',
            'mongo' => 'mongo://mongodb.fspot/fspot?replicaSet=fspot'
        ]);

        $this->assertEquals($facade('mongo'), $facade('mongo'));
        $this->assertEquals($facade('mysql'), $facade('mysql'));
        $this->assertEquals($facade('redis'), $facade('redis'));
    }
}

# vim:set ft=php.phpunit :
