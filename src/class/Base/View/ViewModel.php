<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\View;

use Nora\Scope\Scope;
use Nora\Scope\CallMethodIF;
use Nora\Base\Component\Component;

/**
 * ViewModel用コンポーネント
 */
class ViewModel extends Component implements CallMethodIF
{
    protected function initComponentImpl()
    {
    }

    /**
     * コールさせる
     */
    public function isCallable($name, $parms, $client = null)
    {
        return $this->scope()->isCallable($name, $params, $client);
    }

    /**
     * コール
     */
    public function call($name, $params, $client = null)
    {
        return $this->scope()->call($name, $params, $client);
    }


    /**
     * @helper
     */
    public function urlencode($v)
    {
        return urlencode($v);
    }

    /**
     * @helper
     */
    public function urldecode($v, $not = null)
    {
        return urldecode($v);
    }

    /**
     * @helper
     */
    public function chop($text, $wc = 120, $mark = '...') 
    {
        if (mb_strlen($text) < $wc)
        {
            return $text;
        }else{
            return mb_substr($text, 0, $wc).$mark;
        }
    }
}
