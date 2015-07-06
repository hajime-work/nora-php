<?php
namespace Nora\Data\DataSource\Cursor;

use Nora\Data\DataSource\Handler\Handler;
use Nora\Base\Hash\Hash;
use Nora;

use IteratorAggregate;

/**
 * データソースハンドラ
 */
class Cursor implements IteratorAggregate
{
    private $_handler, $_query, $_options;

    public function __construct(Handler $handler, $query=[], $options = [])
    {
        $this->_handler = $handler;
        $this->_query = $query;
        $this->_options = Nora::Hash($options);
    }

    public function getIterator()
    {
        return $this->_handler->find($this);
    }

    public function getQuery()
    {
        return $this->_query;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function query($q)
    {
        $this->_query = array_merge(
            $this->_query,
            $q
        );
        return $this;
    }

    public function limit($int)
    {
        $this->_options[__function__] = $int;
        return $this;
    }

    public function offset($int)
    {
        $this->_options[__function__] = $int;
        return $this;
    }

    public function order($array)
    {
        $this->_options[__function__] = $array;
        return $this;
    }
}
