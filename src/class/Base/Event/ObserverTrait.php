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
trait ObserverTrait
{
    public function notify(EventIF $ev)
    {
        return $ev;
    }
}
