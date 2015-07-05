<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Model\Base;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Model;
use Nora\DataSource;
use Nora;
use IteratorAggregate;

/**
 * 検索カーソル
 */
class Cursol implements IteratorAggregate
{
    private $_query;
    private $_options = [];
    private $_handler;

    public function __construct(ModelHandler $handler, $query)
    {
        $this->_handler = $handler;
        $this->_query = $query;
    }

    /**
     * 検索件数を取得
     *
     * @return int|Cursol
     */
    public function count (&$output = false)
    {
        $cnt = $this->_handler->count($this->_query);

        if ($output === false)
        {
            return $cnt;
        }
        $output = $cnt;
        return $this;
    }

    public function each($func)
    {
        foreach($this as $k=>$v)
        {
            $func($v, $k);
        }
    }

    public function getIterator( )
    {
        foreach($this->_handler->findReal($this->_query, $this->_options) as $v)
        {
            yield $this->_handler->onGetFilter($v);
        }
    }

}
