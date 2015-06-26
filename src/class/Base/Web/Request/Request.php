<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web\Request;

use Nora;
use Nora\Base\Hash\Hash;


/**
 * リクエスト処理
 */
class Request
{
    private $_url;
    private $_method;

    private $_matched = false;
    private $_post = false;
    private $_get = false;
    private $_data = false;

    /**
     * REQUEST URLを取得する
     *
     * @return string
     */
    public function url( )
    {
        if (!$this->_url){
            $this->_url = Nora::Environment( )->getEnv('REQUEST_URI', '/');
        }
        return $this->_url;
    }

    /**
     * REQUEST Methodを取得する
     *
     * @return string
     */
    public function method( )
    {
        if (!$this->_method){
            $this->_method = Nora::Environment( )->getEnv('REQUEST_METHOD', 'GET');
        }
        return $this->_method;
    }

    /**
     * To String
     */
    public function __toString( )
    {
        return sprintf("%s %s",
            $this->method(),
            $this->url()
        );
    }

    /**
     * マッチしたパラメタを取得する
     */
    public function matched( )
    {
        if ($this->_matched === false)
        {
            $this->_matched = Hash::newHash([], Hash::OPT_IGNORE_CASE | Hash::OPT_ALLOW_UNDEFINED_KEY);
        }
        return $this->_matched;
    }

    /**
     * ポストされたパラメタを取得する
     */
    public function post( )
    {
        if ($this->_post === false)
        {
            $this->_post = Hash::newHash($_POST, Hash::OPT_IGNORE_CASE | Hash::OPT_ALLOW_UNDEFINED_KEY);
        }
        return $this->_post;
    }

    /**
     * GETされたパラメタを取得する
     */
    public function get( )
    {
        if ($this->_get === false)
        {
            $this->_get = Hash::newHash($_GET, Hash::OPT_IGNORE_CASE | Hash::OPT_ALLOW_UNDEFINED_KEY);
        }
        return $this->_get;
    }

    /**
     * まとめて取得する
     */
    public function data( )
    {
        if ($this->_data === false)
        {
            $this->_data = new RequestDatas($this);
        }
        return $this->_data;
    }
}
