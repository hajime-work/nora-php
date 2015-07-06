<?php
namespace Nora\Network\Socket;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testMain()
    {
        $sock = Client::connect(
            '172.17.0.4:25'
        );


        var_dump($sock->read());
        var_dump($sock->write('HELO hajime.work'));
        var_dump($sock->read());
    }
}

# vim:set ft=php.phpunit :
