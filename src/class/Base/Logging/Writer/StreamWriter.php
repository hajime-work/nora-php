<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.1.0
 */
namespace Nora\Base\Logging\Writer;

use Nora\Base\Logging\Log;
use Nora\Base\Logging\LogLevel;
use Nora;
use Nora\Util\Spec\SpecLine;

/**
 * ストリームライター
 */
class StreamWriter
{
    public function __construct(SpecLine $spec)
    {
        $this->_stream = $spec->host() === 'stdout' ?
            fopen('php://stdout', 'w'):
            fopen('php://stderr', 'w');
    }

    public function write($log)
    {
        if (Nora::Environment()->is('commandLine'))
        {
            fwrite($this->_stream, $log."\n");
        }else{
            fwrite($this->_stream, $log."<br />\n");
        }
    }
}
