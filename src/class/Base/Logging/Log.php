<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.1.0
 */
namespace Nora\Base\Logging;

/**
 * ログレベルを保持したり、変換したりするクラス
 */
class Log
{
    private $_level;
    private $_message;
    private $_context;
    private $_tags;

    static public function create ($level, $message, $tags = [], $context = [])
    {
        $log = new Log;
        $log->_level = $level;
        $log->_message = $message;
        $log->_context = $context;
        $log->_tags = $tags;
        return $log;
    }

    public function getLevel()
    {
        return $this->_level;
    }

    public function getMessage()
    {
        return $this->_message;
    }

    public function getContext()
    {
        return $this->_context;
    }

    public function getTags()
    {
        return !is_array($this->_tags) ? []: $this->_tags;
    }
}
