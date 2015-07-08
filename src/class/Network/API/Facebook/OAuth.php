<?php
namespace Nora\Network\API\Facebook;

use Nora\Network\API\OAuth\OAuth as Base;
use Nora\Network\API\OAuth\Consumer;
use Nora\Network\API\OAuth\Token;
use Nora\Network\API\OAuth\Request;
use Nora\Base\Event;
use Nora\Base\Component\Componentable;
use Nora\Util\Util;
use Nora;

/**
 * Facebook用のOAuth
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

    public function requestTokenUrl(Consumer $consumer, $callback, $scope)
    {
        $authURL = 'http://www.facebook.com/dialog/oauth?client_id='.$consumer->key().'&redirect_uri='.urlencode($callback).'&scope='.implode(",",$scope);
        return $authURL;
    }

    public function accessToken(Consumer $consumer, $code, $callback)
    {
        $tokenURL = sprintf(
            'https://graph.facebook.com/oauth/access_token?client_id=%s&redirect_uri=%s&client_secret=%s&code=%s',
            $consumer->key(),
            urlencode($callback),
            $consumer->secret(),
            $code);

        $res = $this->_http->client()->get($tokenURL);

        if ($res->code() !== 200)
        {
            throw new \Exception((string) $res);
        }

        parse_str($res->body(), $v);
        return $v;
    }

    /*
    public function accessToken(Consumer $consumer, $token, $verifier)
    {
        if ($this->_session['token'] !== $token)
        {
            throw new OAuthException('不正なトークンです');
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
     */

}
