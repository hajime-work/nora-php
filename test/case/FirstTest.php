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
        Nora::help('a');
        echo 'イェイ';
    }
}

# vim: set ft=php.phpunit :
