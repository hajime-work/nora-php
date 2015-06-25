<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.1.0
 */
namespace Nora\Base\Logging\Formatter;

use Nora\Base\Logging\Log;
use Nora\Base\Logging\LogLevel;
use Nora;

/**
 * ログをフォーマットする
 *
 * from Environment: %h, %u, %d...
 */
class StringFormatter
{
    private $_format = "%user@%host [%date] *%level* %message %tags %ua";
    private $_format_pre = false;

    public function format(Log $log)
    {
        if (!$this->_format_pre) $this->_format_pre = Nora::Environment()->info($this->_format);

        
        $formated = preg_replace_callback('/%([a-zA-Z_-]+)/', function ($m) use ($log){

            switch($m[1])
            {
            case 'level':
                return LogLevel::toString($log->getLevel());
                break;
            case 'message':
                $parts = [];
                foreach($log->getMessage() as $k=>$v)
                {
                    if (!is_string($v))
                    {
                        $v = print_r($v, true);
                    }
                    $parts[] = sprintf("%s:%s", $k,$v);
                }
                return implode(", ", $parts);
                break;
            case 'tags':
                return implode(",", $log->getTags());
                break;
            default:
                return $m[0];
            }

        }, $this->_format_pre);

        return $formated;
    }

}
