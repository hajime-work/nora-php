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
 * プロファイラ
 */
class Profiler
{
    private $_profile;
    private $_lastTime;
    private $_timer;

    public function init( )
    {
        register_tick_function([$this,'doProfile']);
        register_shutdown_function([$this, 'showProfile']);
        $this->_profile = array();
        $this->_lastTime = microtime(true);
    }


    public function doProfile() {
        $bt = debug_backtrace();
        if (count($bt) <= 1) {
            return;
        }
        $frame = $bt[1];
        unset($bt);
        $function = $frame['function'];

        if (!isset($this->_profile[$function])) {
            $this->_profile[$function] = array(
                'time'  => 0,
                'calls' => 0
            );
        }
        $this->_profile[$function]['calls']++;
        $this->_profile[$function]['time'] += (microtime(true) - $this->_lastTime);
        $this->_lastTime = microtime(true);
    }

    public function showProfile( )
    {
        print_r($this->_profile);
    }
}
