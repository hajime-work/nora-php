<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Profiler;

/**
 * タイマー
 */
class Timer
{
    public function __construct( )
    {
        // register_shutdown_function([$this, 'show']);
        $this->_start = microtime(true);
        $this->_last = microtime(true);
    }

    public function mark($name)
    {
        $time = microtime(true);
        
        $this->_timers[]  = [
            $name,
            $time - $this->_start,
            $time - $this->_last,
        ];
        $this->_last = microtime(true);
    }

    public function show( )
    {
        printf("Timer");
        printf(" ------------------------------\n");
        foreach($this->_timers as $v)
        {
            $name = $v[0];
            $from_start = $v[1];
            $from_last = $v[1];
            printf("%-30s: %s", $name, $from_last);
            echo PHP_EOL;
        }
    }
}
