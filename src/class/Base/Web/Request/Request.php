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
    private $_put = false;
    private $_get = false;
    private $_data = false;
    private $_prefix = false;

    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }

    /**
     * URLをセットする
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * REQUEST URLを取得する
     *
     * @return string
     */
    public function url( )
    {
        if (!$this->_url){
            $this->_url = parse_url(Nora::Environment( )->getEnv('REQUEST_URI', '/'), PHP_URL_PATH);
        }

        if ($this->_prefix === false) return $this->_url;

        if (false === $p = strpos($this->_url, $this->_prefix))
        {
            return $this->_url;
        }

        $url = strlen($this->_prefix) === strlen($this->_url) ? '/': substr($this->_url, strlen($this->_prefix));
        return $url;
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
     * PUTされたパラメタを取得する
     */
    public function put( )
    {
        if ($this->_put === false)
        {
            $put = Nora::Environment()->getPutDatas();
            $this->_put = Hash::newHash($put, Hash::OPT_IGNORE_CASE | Hash::OPT_ALLOW_UNDEFINED_KEY);
        }
        return $this->_put;
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

    /**
     * セット
     */
    public function set($key, $value = null)
    {
        if (is_array($key))
        {
            foreach($key as $k => $v) $this->set($k, $v);
            return $this;
        }

        $this->data()->setVal($key, $value);
        return $this;
    }

    /**
     * 安全にデータを取得する
     *
     * サニタイザを渡す
     */
    public function safeGet($sanitizer)
    {
        return $sanitizer->sanitize($this->data());
    }
}
