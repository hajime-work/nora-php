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


/**
 * リクエスト処理
 */
class Request
{
    private $_url;
    private $_method;

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
}
