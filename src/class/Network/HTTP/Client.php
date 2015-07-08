<?php
namespace Nora\Network\HTTP;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Network\API\Twitter;

/**
 * HTTP Client
 */
class Client extends Component
{
    private $_options;
    private $_headers;

    const AUTOREFERER     = 100;
    const REFERER         = 200;
    const RETURNTRANSFER  = 1;
    const TIMEOUT         = 10;
    const HTTPHEADER      = 3;
    const HEADER          = 4;
    const HEADER_OUT      = 410;
    const HEADERFUNCTION  = 420;
    const SSL_VERIFYPEER  = 5;
    const SSL_VERIFYHOST  = 6;
    const URL             = 7;
    const FOLLOWLOCATION  = 8;
    const MAXREDIRS       = 81;
    const USERAGENT       = 9;
    const COOKIE          = 10;
    const COOKIEJAR       = 11;
    const COOKIEFILE      = 12;
    const POST            = 20;
    const POSTFIELDS      = 21;
    const VERBOSE         = 99;
    const CUSTOMREQUEST   = 98;
    const HTTPAUTH        = 80;
    const USERPWD         = 81;
    const AUTH_BASIC      = CURLAUTH_BASIC;
    const PROXY           = 1004;
    const PROXYPORT       = 59;
    const HTTPPROXYTUNNEL = 61;
    const PROXYUSERPWD    = 1006;
    const PROXYTYPE       = 101;

    protected function initComponentImpl( )
    {
        $this->_options = [];

        $this->setOpt([
            'RETURNTRANSFER' => 1,
            'TIMEOUT'        => 2,
            'HEADER'         => 1,
            'SSL_VERIFYPEER' => false,
            'SSL_VERIFYHOST' => 0,
            'HEADER_OUT'     => true,
            'AUTOREFERER'    => true,
            'HEADER'         => 1,
            'USERAGENT'      => 'Nora HTTP User Agent',
        ]);

        $this->_headers = [];
    }

    static public function getOptInt($name)
    {
        return constant('self::'.strtoupper($name));
    }

    public function getOpts($array)
    {
        return array_merge($this->_options, $array);
    }

    public function getHeaders($array)
    {
        return array_merge($this->_headers, $array);
    }

    public function setOpt($name, $value = null)
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v) $this->setOpt($k, $v);
            return $this;
        }
        $this->_options[strtoupper($name)] = $value;
        return $this;
    }

    /**
     * ポストリクエスト
     */
    public function post($url, $datas = [], $headers = [])
    {
        $curl = new Curl\Curl();
        $curl->setOpt($this->getOpts([
            'url' => $url,
            'post' => 1,
            'postfields' => (is_array($datas) ? http_build_query($datas): $datas),
            'httpheader' => $this->getHeaders($headers),
        ]));

        $curl->execute();
        return $curl;
    }

    /**
     * ゲットリクエスト
     */
    public function get($url, $datas = [], $headers = [])
    {
        if (!empty($datas))
        {
            if (is_string($datas))
            {
                $url.='?'.$datas;
            }elseif(is_array($datas)){
                $url.='?'.http_build_query($datas);
            }
        }
        $curl = new Curl\Curl();
        $curl->setOpt($this->getOpts([
            'url' => $url,
            'httpheader' => $this->getHeaders($headers),
        ]));

        $curl->execute();
        return $curl;
    }

    /**
     * リクエスト
     */
    public function request(RequestIF $req)
    {
        $res = $this->{$req->getMethod()}(
            $req->getURL(),
            $req->getParams(),
            $req->getHeaders()
        );

        if ($res->code() === 200)
        {
            return $res->body();
        }

        throw new Exception\RequestException($res);
    }
}
