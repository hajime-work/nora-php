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

use ArrayAccess;

/**
 * リクエスト処理
 */
class RequestDatas extends Hash
{
    private $_req;
    private $_keys = ['matched', 'post', 'get'];

    public function __construct(Request $req)
    {
        $this->_req = $req;
    }


    public function &getVal($key, $default = null)
    {
        if (parent::hasVal($key)) return parent::getVal($key);

        foreach($this->_keys as $v)
        {
            if ( $this->_req->$v()->hasVal($key) )
            {
                return $this->_req->$v()->getVal($key);
            }
        }
        return $default;
    }

    public function hasVal($key)
    {
        if (parent::hasVal($key)) return true;

        foreach($this->_keys as $v)
        {
            if( $this->_req->$v()->hasVal($key) )
            {
                return true;
            }
        }
        return false;
    }

    public function getKeys()
    {
        $keys = parent::getKeys();

        foreach($this->_keys as $v)
        {
            $keys += $this->_req->$v()->getKeys();
        }

        return $keys;
    }
}
