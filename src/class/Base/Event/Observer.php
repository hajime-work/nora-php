<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Event;

use Nora\Base\Hash\Hash;

/**
 * イベントオブザーバ
 */
class Observer implements ObserverIF
{
    private $_handler;

    static public function create ($cb)
    {
        $ob = new Observer();
        $ob->_handler = $cb;
        return $ob;
    }

    public function notify(EventIF $ev)
    {
        call_user_func($this->_handler, $ev);
        return $ev;
    }
}
