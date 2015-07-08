<?php
namespace Nora\Network\API\OAuth;

use Nora\Base\Component\Componentable;
use Nora\Network\HTTP\RequestIF;
use Nora\Base\Hash\Hash;

/**
 * OAuth Request
 */
class Request extends Hash implements RequestIF
{
    use Componentable;

    private $_consumer, $_token, $_method, $_url, $_params;

    static public function createRequest(Consumer $consumer, Token $token = null)
    {
        $req = new Request( );
        $req->_consumer = $consumer;
        if ($token !== null)
        {
            $req->_token = $token;
        }
        $req->_init( );
        return $req;
    }

    private function _init( )
    {
        $this->initValues([
            'oauth_nonce' => md5(uniqid(rand(), true)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
            'oauth_consumer_key' => $this->_consumer->key(),
        ]);

        $this->set_hash_option(Hash::OPT_ALLOW_UNDEFINED_KEY);

        if (!empty($this->_token)) $this->setVal('oauth_token', $this->_token->key());

    }

    protected function initComponentImpl( )
    {

    }

    public function signe($method, $url)
    {
        // 署名対象
        $params = $this->toArray();

        if (!empty($this->_params))
        {
            $params = array_merge($params, $this->_params);
        }
        ksort($params);
        $string = implode('&', [$method, urlencode($url), urlencode(http_build_query($params))]);

        // 署名キー
        $key = implode('&', array_map('urlencode', [
            $this->_consumer->secret(), null !== $this->_token ? $this->_token->secret() : ""
        ]));

        // 署名
        return base64_encode(hash_hmac('sha1', $string, $key, true));
    }

    public function getOAuthHeader($method, $url)
    {
        $params = $this->toArray();
        $params['oauth_signature'] = $this->signe($method, $url);
        ksort($params);

        $array = [];
        foreach($params as $k=>$v)
        {
            $array[] = sprintf('%s="%s"', $k, urlencode($v));
        }

        return implode(", ", $array);
    }

    public function get($url, $params = [])
    {
        $this->_url = $url;
        $this->_params = $params;
        $this->_method = 'GET';
        return $this;
    }

    public function getMethod( )
    {
        return $this->_method;
    }

    public function getHeaders( )
    {
        return [
            'Authorization: OAuth '.$this->getOAuthHeader($this->getMethod(), $this->getURL())
        ];
    }

    public function getParams() 
    {
        return $this->_params;
    }

    public function getURL()
    {
        return $this->_url;
    }
}
