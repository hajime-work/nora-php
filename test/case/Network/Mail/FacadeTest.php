<?php
namespace Nora\Network\Mail;

use Nora;

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
        $logger = Nora::Logger()->newLogger([
            'name' => 'test',
            'writer' => 'stream://stdout',
            'format' => '[MAIL-LOG] %message'
        ]);

        $facade = new Facade();
        $facade->setSMTP('172.17.0.4:25');
        $facade->attach($logger);


        // SMTP\Transportのテスト
        $smtp = $facade->smtp();

        $smtp
            ->mailFrom('m@hajime.work')
            ->rcptTo('hajime@avap.co.jp')
            ->dataStart()
            ->data("From: m@hajime.work")
            ->data()
            ->data("本文")
            ->dataEnd();

        $facade->setSender('m@hajime.work');
        $facade->sendMail('m@hajime.work', 'こんにちは', '本文');
    }
}

# vim:set ft=php.phpunit :
