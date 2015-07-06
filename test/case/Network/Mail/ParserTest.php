<?php
namespace Nora\Network\Mail;

use Nora;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testMain()
    {
        $dir = TEST_DIR.'/post_mail_dir';
        $facade = new Facade();
        $facade->setSMTP('postfix-gw:25');

        $logger = Nora::Logger()->newLogger([
            'name' => 'test',
            'writer' => 'stream://stdout',
            'format' => '[MAIL-LOG] %message'
        ]);

        $facade->attach( $logger );


        $cnt = 1;
        foreach(glob($dir.'/___post*') as $file)
        {
            $mail = $facade->parse($file);

            // 送信者のアドレス
            printf("%000d %s from %s".PHP_EOL, $cnt++, $mail->getSubject(true), $mail->getFrom());


            // 木暮さんへの嫌がらせ
            $mail->clearRcptTo();
            $mail->rcptTo('dogacenter1@yahoo.co.jp');
            $mail->to('dogacenter1@yahoo.co.jp');
            $mail->mailFrom('m@hajime.work');
            $mail->removeHeader('Date');
            $facade->send($mail);
            echo $mail;
            die();
            
            /*
            */

        }
    }
}

# vim:set ft=php.phpunit :
