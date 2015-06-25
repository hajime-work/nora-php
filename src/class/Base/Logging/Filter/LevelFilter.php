<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.1.0
 */
namespace Nora\Base\Logging\Filter;

use Nora\Base\Logging\Log;
use Nora\Base\Logging\LogLevel;
use Nora;
use Nora\Util\Spec\SpecLine;

/**
 * フィルタ
 */
class LevelFilter
{
    public function __construct(SpecLine $spec)
    {
        $this->_level = LogLevel::toInt($spec->host());
    }

    public function filter($log)
    {
        //return ($log->getLevel() ^ $this->level) > 0;
        return $log->getLevel() <= $this->_level ? true: false;
    }
}
