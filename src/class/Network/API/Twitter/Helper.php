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
class Helper
{
    private $_user = false;
    private $_facade;
    private $_consumer;
    private $_token;

    public function __construct(Facade $facade, Consumer $con, Token $token)
    {
        $this->_facade   = $facade;
        $this->_consumer = $con;
        $this->_token    = $token;
    }

    /**
     * アカウント情報を取得
     *
     * @return array
     */
    public function account( )
    {
        return $this->get('/account/settings.json');
    }

    /**
     * アカウント情報を検証
     *
     * @return array
     */
    public function verify( )
    {
        try
        {
            $data = $this->get('/account/verify_credentials.json');
            $this->_user = $data;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 名前を取得する
     */
    public function name()
    {
        return $this->user()['name'];
    }

    /**
     * スクリーン名を取得する
     */
    public function screenName()
    {
        return $this->user()['screen_name'];
    }


    /**
     * ユーザ情報を取得
     */
    public function user( )
    {
        if ($this->_user === false)
        {
            if (!$this->verify())
            {
                throw new \Exception('ログインしていません');
            }
            return $this->_user;
        }
        return $this->_user;
    }

    public function get($url)
    {
        return json_decode(
            $this->_facade->oauth()->get(
                $this->_consumer,
                $this->_token,
                $url
            ),
            true
        );
    }
}
