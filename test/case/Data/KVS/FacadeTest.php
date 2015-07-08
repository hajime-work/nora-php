<?php
namespace Nora\Data\KVS;
use Nora\Data\DataBase;

class FacadeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testDir()
    {
        $DB = new DataBase\Facade();
        $connection = 'dir:///tmp/hoge';
        $facade = new Facade( );
        $facade->setDBHandler($DB);
        $storage = $facade->getStorage($connection);

        $storage->set('ほげ', $connection);

        var_Dump($storage->get('ほげ'));

        var_Dump($storage->delete('ほげ'));

        var_Dump($storage->get('ほげ'));

    }

}

# vim:set ft=php.phpunit :
