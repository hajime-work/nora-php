<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Component;

use Nora\Base\Component\Componentable;
use Nora\Base\Logging\Logger as Base;
use Nora\Base\Event;
use Nora\Base\Logging\LogLevel;
use Nora\Base\Logging\Log;

/**
 * ロガー
 */
class Logger extends Base implements Event\ObserverIF
{
    use Componentable;

    protected function initComponentImpl( )
    {
    }

    public function __invoke($client, $params = [])
    {
        return $this;
    }
}
