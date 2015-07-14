<?php
namespace Nora\Network\API\Twitter;

use Nora\Network\API\OAuth\OAuth as Base;
use Nora\Network\API\OAuth\Consumer;
use Nora\Network\API\OAuth\Token;
use Nora\Network\API\OAuth\Request;
use Nora\Base\Event;
use Nora\Base\Component\Componentable;
use Nora\Util\Util;
use Nora;

/**
 * Twitter用のOAuth
 */
class OAuth extends Base
{
    use Componentable;

    private $_http;
    private $_session;

    protected function initComponentImpl( )
    {
        $this->injection(['HTTP', 'Session', function($http, $session) {
            $this->_http = $http;
            $this->_session = $session;
        }]);
    }

    public function requestTokenUrl(Consumer $consumer, $callback)
    {
        $url = Config::OAUTH_ENDPOINT.'/oauth/request_token';
        $method = 'GET';

        $res = $this->_http->client()->request(
            Request::createRequest($consumer)
                ->get($url)
                ->setVal('oauth_callback', $callback) // コールバック先を仕込む
        );

        parse_str($res, $v);

        // URL
        return [
            'token' => $v['oauth_token'],
            'url' => sprintf("https://api.twitter.com/oauth/authorize?oauth_token=%s", $v['oauth_token'])
        ];
    }

    public function accessToken(Consumer $consumer, $token, $token_prev, $verifier)
    {
        if ($token_prev !== $token)
        {
            throw new OAuthException("不正なトークンです");
        }
        $url = Config::OAUTH_ENDPOINT.'/oauth/access_token';

        $req = Request::createRequest($consumer)->get($url);
        $req['oauth_token'] = $token;
        $req['oauth_verifier'] = $verifier;

        $client = $this->injection(['HTTP', function($http) {
            return $http->client();
        }]);
        $res = $client->request($req);

        parse_str($res, $v);

        $this->_session['access_token'] = $v;

        return $v;
    }

    public function get(Consumer $consumer, Token $token, $url, $params = [])
    {
        $url = Config::OAUTH_ENDPOINT.'/1.1'.$url;
        $req = Request::createRequest($consumer, $token)->get($url, $params);
        return $this->injection(['HTTP', function($http) use ($req) {
            return $http->client()->request($req);
        }]);
    }

}
