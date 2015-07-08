<?php
namespace Nora\Network\HTTP\Curl;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Network\API\Twitter;
use Nora\Network\HTTP\Client;

/**
 * Curl Wrapper
 */
class Curl
{
    private static $map =
        [
            Client::AUTOREFERER     => CURLOPT_AUTOREFERER,
            Client::REFERER         => CURLOPT_REFERER,
            Client::RETURNTRANSFER  => CURLOPT_RETURNTRANSFER,
            Client::TIMEOUT         => CURLOPT_TIMEOUT,
            Client::HTTPHEADER      => CURLOPT_HTTPHEADER,
            Client::HEADER          => CURLOPT_HEADER,
            Client::HEADER_OUT      => CURLINFO_HEADER_OUT,
            Client::HEADERFUNCTION  => CURLOPT_HEADERFUNCTION,
            Client::SSL_VERIFYPEER  => CURLOPT_SSL_VERIFYPEER,
            Client::SSL_VERIFYHOST  => CURLOPT_SSL_VERIFYHOST,
            Client::URL             => CURLOPT_URL,
            Client::FOLLOWLOCATION  => CURLOPT_FOLLOWLOCATION,
            Client::MAXREDIRS       => CURLOPT_MAXREDIRS,
            Client::USERAGENT       => CURLOPT_USERAGENT,
            Client::COOKIE          => CURLOPT_COOKIE,
            Client::COOKIEJAR       => CURLOPT_COOKIEJAR,
            Client::COOKIEFILE      => CURLOPT_COOKIEFILE,
            Client::POST            => CURLOPT_POST,
            Client::POSTFIELDS      => CURLOPT_POSTFIELDS,
            Client::VERBOSE         => CURLOPT_VERBOSE,
            Client::CUSTOMREQUEST   => CURLOPT_CUSTOMREQUEST,
            Client::HTTPAUTH        => CURLOPT_HTTPAUTH,
            Client::USERPWD         => CURLOPT_USERPWD,
            Client::PROXY           => CURLOPT_PROXY,
            Client::PROXYPORT       => CURLOPT_PROXYPORT,
            Client::HTTPPROXYTUNNEL => CURLOPT_HTTPPROXYTUNNEL,
            Client::PROXYUSERPWD    => CURLOPT_PROXYUSERPWD,
            Client::PROXYTYPE       => CURLOPT_PROXYTYPE,

        ];

    private $_ch;
    private $_result;
    private $_info;

    public function __construct( )
    {
        $this->_ch = curl_init();
    }

    public function setOpt($name, $value = null)
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v)
            {
                $this->setOpt($k, $v);
            }

            return $this;
        }

        if (is_string($name))
        {
            $name = Client::getOptInt($name);
        }

        curl_setopt(
            $this->_ch,
            self::$map[$name],
            $value
        );
    }

    public function execute( )
    {
        $this->_result = curl_exec($this->_ch);
        $this->_info  = curl_getinfo($this->_ch);
        curl_close($this->_ch);
        return $this;
    }

    public function getResult( )
    {
        return $this->_result;
    }

    public function getInfo( )
    {
        return $this->_info;
    }

    public function header()
    {
        return substr($this->getResult(), 0, $this->headerSize());
    }


    public function body()
    {
        return substr($this->getResult(), $this->headerSize());
    }

    public function code()
    {
        return $this->getInfo()['http_code'];
    }

    public function headerSize()
    {
        return $this->getInfo()['header_size'];
    }

    public function __toString( )
    {
        return (string) $this->getResult();
    }
}
