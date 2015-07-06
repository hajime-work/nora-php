<?php
class FirstTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testA( )
    {
        var_Dump(Nora::DataSource( )->getDataSource('shop'));
    }
}

# vim: set ft=php.phpunit :
