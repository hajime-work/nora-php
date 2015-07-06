<?php
namespace Nora\Data\Cache;
use Nora\Data\DataBase;

class FacadeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testMain()
    {
        $DB = new DataBase\Facade();
        $DB->setConnection([
            'redis' => 'redis://redis01.fuzoku.gallery/fspot-cache'
        ]);

        $Cache = new Facade( );
        $Cache->setDBHandler($DB);
        $Cache->connect('redis://test');

        $handler = $Cache->getHandler('test');
        $handler->delete('key');
        $handler->useCache('key', function(&$s) {
            $s = true;
            return time();
        }, -1, -1, $st);
        $this->assertEquals('created', $st);

        $handler->useCache('key', function(&$s) {
            $s = true;
            return time();
        }, -1, -1, $st);
        $this->assertEquals('found', $st);
    }
}

# vim:set ft=php.phpunit :
