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
class LogLevel
{
    const EMERG   = 1;
    const ALERT   = 2;
    const CRIT    = 4;
    const ERR     = 8;
    const WARNING = 16;
    const NOTICE  = 32;
    const INFO    = 64;
    const DEBUG   = 128;
    const ALL     = 255;

    /**
     * シスログのレベルへ変換する
     *
     * @param int $level
     * @return int
     */
    static public function toSyslog ($level)
    {
        switch ($level)
        {
        case self::EMERG:
            return 0;

        case self::ALERT:
            return 1;

        case self::CRIT:
            return 2;

        case self::ERR:
            return 3;

        case self::WARNING:
            return 4;

        case self::NOTICE:
            return 5;

        case self::INFO:
            return 6;

        case self::DEBUG:
            return 7;
        }

        return false;
    }

    /**
     * ログレベルを文字列に変換する
     *
     * @param int $level
     * @return string
     */
    static public function toString ($level)
    {
        switch ($level)
        {
        case self::EMERG:
            return 'EMERG';

        case self::ALERT:
            return 'ALERT';

        case self::CRIT:
            return 'CRIT';

        case self::ERR:
            return 'ERR';

        case self::WARNING:
            return 'WARNING';

        case self::NOTICE:
            return 'NOTICE';

        case self::INFO:
            return 'INFO';

        case self::DEBUG:
            return 'DEBUG';
        }
        return false;
    }

    /**
     * 文字列をログレベルに変換する
     *
     * @param string $level
     * @return int
     */
    static public function toInt ($level)
    {
        switch (strtoupper($level))
        {
        case 'EMERG':
            return self::EMERG;

        case 'ALERT':
            return self::ALERT;

        case 'CRIT':
            return self::CRIT;

        case 'ERR':
            return self::ERR;

        case 'WARNING':
            return self::WARNING;

        case 'NOTICE':
            return self::NOTICE;

        case 'INFO':
            return self::INFO;

        case 'DEBUG':
            return self::DEBUG;
        }

        throw new ApplicationError(sprintf(
            __('%sはログレベルにありません'), $level
        ));
        return false;
    }


    /**
     * PHPのログレベルを変換する
     */
    static public function phpToNora($level)
    {
        switch($level)
        {
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
            return self::CRIT;
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            return self::ERR;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            return self::WARNING;
        case E_NOTICE:
            return self::INFO;
        case E_USER_NOTICE:
        case E_STRICT:
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            return self::INFO;
        }

        return false;
    }
}
