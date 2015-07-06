<?php
namespace Nora\Network\Socket;

use Nora\Core\Event\EventClientTrait;

/**
 * SocketClient
 */
class Client extends Socket
{
    const BS = 1024;

    private $con;


    /**
     * 接続をする
     */
    static public function connect ($spec)
    {
        $con = stream_socket_client($spec, $eno, $emsg);

        if (!$con)
        {
            throw new Exception\SocketConnectFaild($spec, $eno.' '.$emsg);
        }

        $sock = new static( );
        $sock->con = $con;

        return $sock;
    }


    /**
     * 暗号化
     */
    public function crypt($mode = null)
    {
        if ($mode === null)
        {
            stream_socket_enable_crypto($this->con, true);
        }
        else
        {
            stream_socket_enable_crypto($this->con, true, $mode);
        }

        $this->logDebug('crypte mode='.$mode);
    }

    /**
     * ソケットへ書き込む
     */
    public function write ($buf = '')
    {
        $buf.="\n";
        $this->writeBuffer($this->con, $buf);
        return $buf;
    }

    /**
     * ソケットへ書き込む:フォーマット
     */
    public function writef ($buf)
    {
        return $this->write( vsprintf(
            $buf,
            array_slice(func_get_args(), 1)));
    }

    /**
     * ソケットから読み込む
     */
    public function read ( )
    {
        $buf = $this->readBuffer($this->con);
        return $buf;
    }
}
