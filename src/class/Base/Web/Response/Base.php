<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web\Response;

use Nora;


/**
 * レスポンス制御
 */
class Base
{
    private $_exit_method;
    private $_body;
    private $_headers = [];
    private $_status = 200;

    /**
     * @var array HTTP status codes {{{
     */
    public static $codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ); // }}}

    public function __construct ( )
    {
        $this->_exit_method = function( ) {
            exit(0);
        };
    }

    /**
     * レスポンスを空にする
     *
     * @return Response
     */
    public function clear( )
    {
        $this->_body = "";
        $this->_status = 200;
        $this->_headers = [];
        return $this;
    }

    /**
     * レスポンスを書き込む
     *
     * @param string $body
     * @return Response
     */
    public function write($body)
    {
        $this->_body .= $body;
        return $this;
    }

    /**
     * ステータスをセット
     *
     * @param int $code
     * @return Response
     */
    public function status($code)
    {
        $this->_status = $code;
        return $this;
    }

    /**
     * Adds a header to the response.
     *
     * @param string|array $name Header name or array of names and values
     * @param string $value Header value
     * @return object Self reference
     */
    public function header($name, $value = null) {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->_headers[$k] = $v;
            }
        }
        else {
            $this->_headers[$name] = $value;
        }
        return $this;
    }


    protected function callExit()
    {
        call_user_func($this->_exit_method);
    }

    /**
     * レスポンスを送信する
     */
    public function send( )
    {
        if (ob_get_length() > 0)
        {
            $this->write(ob_get_clean());
        }

        if (!headers_sent()) {
            $this->sendHeaders();
        }


        echo $this->_body;


        $this->callExit();
    }

    public function sendHeaders( )
    {
        if(!isset(self::$codes[$this->_status]))
        {
            // var_dump ($this->_status);
        }

        header(
            sprintf(
                '%s %d %s',
                Nora::Environment()->getEnv('SERVER_PROTOCOL', 'HTTP/1.1'),
                $this->_status,
                self::$codes[$this->_status]),
            true,
            $this->_status
        );

        // Send other headers
        foreach ($this->_headers as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    header($field.': '.$v, false);
                }
            }
            else {
                header($field.': '.$value);
            }
        }
    }

}


/* vim: set foldmethod=marker : */
