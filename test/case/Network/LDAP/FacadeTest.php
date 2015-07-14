<?php
namespace Nora\Network\LDAP;

class Test extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testMain()
    {
        $facade = new facade();

        $facade->connect('openldap.local', 389);
    }
}

# vim:set ft=php.phpunit :
