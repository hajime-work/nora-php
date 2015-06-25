<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Logging;

use Nora\Base\Component\Componentable;
use Nora\Base\Event;
use Nora\Base\Hash\ObjectHash;
use Nora\Util\Spec\SpecLine;

/**
 * ロガー
 */
class Logger implements Event\ObserverIF
{
    private $_formatter;
    private $_children;
    private $_writer;
    private $_filters = [];

    public function __construct( )
    {
        $this->_children = new ObjectHash();
    }

    /**
     * イベントを受け取る
     */
    public function notify(Event\EventIF $ev)
    {
        $log = false;

        if ( $ev->match(['php.error']))
        {
            // ログを作成
            $log = Log::create(
                $level = LogLevel::phpToNora($ev->errno),
                $message = $ev->toArray(),
                $ev->getTags()->toArray(),
                $ev->getContext()
            );
        }elseif($ev->match(['php.exception'])){
            $log = Log::create(
                $level = LogLevel::ERR,
                $message = [
                    'exp' => (string) $ev->exception
                ],
                $ev->getTags()->toArray(),
                $ev->getContext()
            );
        }elseif($ev->match(['log'])) {
            $log = Log::create(
                $level = $ev->level,
                $message = $ev->message,
                $ev->tags,
                $ev->getContext()
            );
        }



        if ($log !== false)
        {
            $this->write($log);
        }
    }

    /**
     * ログフォーマッタを取得する
     */
    protected function formatter( )
    {
        if (!$this->_formatter)
        {
            $this->_formatter = new Formatter\StringFormatter( );
        }
        return $this->_formatter;
    } 

    /**
     * ログを整形する
     *
     * @param Log $log
     * @return string
     */
    protected function format(Log $log)
    {
        return $this->formatter()->format($log);
    }

    /**
     * ログを出力する
     *
     * @param Log $log
     */
    protected function write(Log $log )
    {
        foreach ($this->_filters as $filter)
        {
            if (!$filter->filter($log))
            {
                return;
            }
        }
        if (!$this->_writer && count($this->_children) == 0)
        {
            echo $this->format($log)."\n";
            return;
        }

        if ($this->_writer) $this->_writer->write($this->format($log));

        foreach($this->_children as $logger)
        {
            $logger->write($log);
        }
    }

    /**
     * デバッグ
     *
     * @param mixd $message
     */
    public function debug($message)
    {
        if (!is_array($message)) $message = ['msg' => $message];

        $this->write(
            Log::create(
                LogLevel::DEBUG,
                $message
            )
        );
    }

    /**
     * ロガーを追加する
     */
    public function addLogger($spec)
    {
        $logger = new Logger();

        if (isset($spec['writer']))
        {
            $writer_spec = SpecLine::parse($spec['writer']);
            $class = sprintf(__namespace__.'\\Writer\\%sWriter', ucfirst($writer_spec->scheme('stdout')));
            $logger->_writer = new $class($writer_spec);
        }

        if (isset($spec['filter']))
        {
            foreach($spec['filter'] as $s)
            {
                $filter_spec = SpecLine::parse($s);
                $class = sprintf(__namespace__.'\\Filter\\%sFilter', ucfirst($filter_spec->scheme('level')));
                $logger->_filters[] = new $class($filter_spec);
            }
        }


        $this->_children->add($logger);
        return $this;
    }
}