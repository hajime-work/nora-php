<?php
namespace Nora\Network\Mail;

use Nora\Base\Event;

/**
 * メールハンドラ
 */
class Facade implements Event\ObserverIF, Event\SubjectIF
{
    use Event\SubjectTrait;

    private $_smtp;

    public function notify(Event\EventIF $ev)
    {
        $this->fire($ev);
    }

    public function setSMTP($host)
    {
        $this->_smtp = $host;
        return $this;
    }

    public function setSender($sender)
    {
        $this->_sender = $sender;
        return $this;
    }


    public function smtp()
    {
        $smtp = SMTP\Transport::connect($this->_smtp);
        $smtp->attach($this);
        return $smtp;
    }

    public function sendMail($to, $subject, $body, $headers = [])
    {
        // メールを作成
        $mail = $this->mail( )
            ->subject($subject)
            ->to($to)
            ->from($this->_sender)
            ->plain($body)
            ->addHeader($headers);

        echo "\n";
        echo $mail;
        $this->smtp()->submit($mail);
    }

    /**
     * メールデータをパースする
     */
    public function parse($mail, $only_header = false)
    {
        if (is_file($mail)) {
            $mail = file_get_contents($mail);
        }
        return Parser::parse($mail, $only_header);
    }

    /**
     * メールデータをパースする
     */
    public function parseBuffer($buffer, $only_header = false)
    {
        return Parser::parse($buffer, $only_header);
    }

    /**
     * メールデータを作成する
     */
    public function mail ( )
    {
        $mail = new Mail( );
        return $mail;
    }
}
