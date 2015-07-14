<?php
namespace Nora\Data\CSV;

use Iterator;

class Parser implements Iterator
{
    private $_delimiter = ',';
    private $_enclosure = '"';
    private $_escape    = '\\';
    private $_lines;
    private $_index = 0;

    public function setup($array)
    {
        foreach($array as $k=>$v)
        {
            $this->{"_".$k} = $v;
        }
    }

    static public function parse($file, $array)
    {
        $p = new Parser();
        $p->setup($array);
        $p->parseFile($file);
        return $p;
    }

    public function parseFile($file)
    {
        $this->_lines = file($file);
        return $this;
    }

    public function current( )
    {
        if ($this->valid())
        {
            return str_getcsv($this->_lines[$this->_index]);
        }
        return false;
    }

    public function next( )
    {
        $this->_index++;
    }

    public function key( )
    {
        return $this->_index;
    }

    public function rewind( )
    {
        $this->_index = 0;
    }

    public function valid( )
    {
        return $this->_index < count($this->_lines);
    }

    public function each($cb)
    {
        $header = $this->current();
        $this->next();

        while($data = $this->current())
        {
            $this->next();
            $arg = array_combine($header, $data);
            if ($arg === false)
            {
                var_dump($data);
            }
            $cb($arg);
        }
    }
}

