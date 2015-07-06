<?php
namespace Nora\Network\Mail\SMTP;

use Nora\Network\Socket;
use Nora\Network\Mail\Mail;

use Nora\Base\Logging;
/**
 * SMTP Transporter
 *
 * @author     Hajime MATSUMOTO <hajime.matsumoto@avap.co.jp>
 * @copyright  Since 20014 Nora Project
 * @license    http://nora.avap.co.jp/license.txt
 * @version    $Id:$
 */
class Transport
{
    use Logging\LogEventSubjectTrait;

    private $sock;
    private $addr;
    private $logHandler = null;

    private function __construct (  )
    {
    }

    

    /**
     * Connect
     */
    static public function connect ( $host, $helo = 'localhost', $crypt = false )
    {
        $sock = Socket\Client::connect($host);
        $sock->writef('HELO %s', $helo);

        if ($crypt === true)
        {
            $client->crypt(true);
        }


        $smtp = new Self();
        $smtp->sock = $sock;
        $smtp->logDebug($smtp->sock->read());
        return $smtp;
    }

    /**
     * Send Mail From
     *
     * @param string $mail
     * @return string
     */
    public function mailFrom ( $mail )
    {
        $this->logDebug($this->sock->writef('Mail From: %s', $mail));
        $this->logDebug($this->sock->read());
        return $this;
    }

    /**
     * Send RCTPT To
     *
     * @param string $mail
     * @return string
     */
    public function rcptTo ( $mail )
    {
        $this->logDebug($this->sock->writef('RCPT TO: %s', $mail));
        $this->logDebug($this->sock->read());
        return $this;
    }

    /**
     * Send Simple Text
     *
     * @param string $text
     */
    public function data($text = "\n")
    {
        $this->sock->write($text);
        return $this;
    }

    /**
     * Send DATA
     *
     * @return string
     */
    public function dataStart (  )
    {
        $this->sock->writef('DATA');
        $this->logDebug($this->sock->read());
        return $this;
    }

    /**
     * Send DATA End Signal
     *
     * @return string
     */
    public function dataEnd (  )
    {
        $this->sock->writef(".");
        $this->logDebug($this->sock->read());
        return $this;
    }

    /**
     * Submit Mail
     *
     */
    public function submit ( Mail $mail )
    {
        $this->mailFrom($mail->getMailFrom());
        array_walk($mail->getRcptTo(), function($rcpt) use ($mail) {
            $this->rcptTo($rcpt);
        });
        $this->dataStart();
        $this->data($mail->__toString());
        $this->dataEnd();

    }
}
