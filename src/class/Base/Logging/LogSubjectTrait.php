<?php
namespace Nora\Base\Logging;


trait logSubjectTrait
{
    // ロギング {{{
    
    public function logEmerg($message)
    {
        $this->log(LogLevel::EMERG, $message);
    }

    public function logAlert($message)
    {
        $this->log(LogLevel::ALERT, $message);
    }

    public function logCrig($message)
    {
        $this->log(LogLevel::CRIT, $message);
    }
    public function logErr($message)
    {
        $this->log(LogLevel::ERR, $message);
    }
    public function logWarning($message)
    {
        $this->log(LogLevel::WARNING, $message);
    }
    public function logNotice($message)
    {
        $this->log(LogLevel::NOTICE, $message);
    }
    public function logInfo($message)
    {
        $this->log(LogLevel::INFO, $message);
    }
    public function logDebug($message)
    {
        $this->log(LogLevel::DEBUG, $message);
    }

    /**
     * ログ処理
     *
     * ルートスコープのイベントにログイベントを投げ込む
     *
     * @param int $level
     * @param mixed $message
     * @return void
     */
    abstract function log ($level, $message);
}
