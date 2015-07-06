<?php
namespace Nora\Data\DataBase\Connection;

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
        $mysql = [
            'mysqli://fspot.mysql.slave/fspot?user=fspot&pass=deganjue',
            'mysqli://fspot.mysql.slave/fspot', 
            'mysqli://fspot.mysql.slave', 
            'mysqli://fspot.mysql.slave?user=fspot&pass=deganjue'
        ];

        foreach($mysql as $line)
        {
            $spec = new Spec($line);

            $this->assertEquals('mysqli', $spec->scheme);

            if (isset($spec->field))
            {
                $this->assertEquals('fspot', $spec->field);
            }

            if ($spec->hasAttr('user'))
            {
                $this->assertEquals('fspot', $spec->getAttr('user'));
            }
        }

    }

    public function testPort()
    {
        $mysql = 'mysqli://fspot.mysql.slave:3306';
        $spec = new Spec($mysql);
        $this->assertEquals(3306, $spec->port);
    }
}

# vim:set ft=php.phpunit :
