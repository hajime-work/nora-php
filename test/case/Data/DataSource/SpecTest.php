<?php
namespace Nora\Data\DataSource;

class SpecTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testMain()
    {
        $spec = new Spec('fspot-redis://hoge');

        $this->assertEquals('fspot-redis', $spec->con() );
        $this->assertEquals('hoge', $spec->table() );
    }
}

# vim:set ft=php.phpunit :
