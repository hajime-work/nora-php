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
 * Facebook用のGraphAPI
 */
class Graph extends Base
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


    public function get($token, $url, $params = [])
    {
        $url = 'https://graph.facebook.com'.$url;
        $res = $this->_http->client()->get($url, array_merge([
            'access_token' => $token,
        ], $params));

        if ($res->code() !== 200)
        {
            throw new \Exception('<pre>'.var_export($res->getInfo(), true).(string) $res.'</pre>');
        }
        return $res->body();
    }

}
