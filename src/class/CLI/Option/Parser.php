<?php
namespace Nora\CLI\Option;

use Nora\Base\Component\Componentable;
use Nora\CLI\Exception;
use Nora;

/**
 * CLI:Option Parser
 */
class Parser
{
    private $_options;
    private $_arguments;
    private $_next_argv;
    private $_description;
    private $_parsed_options = false;
    private $_parsed_args = false;

    public function __construct($description, $env)
    {
        $this->_description = $description;
        $this->setArgv($env->getArgv());
    }

    public function addOption($name, $spec = null)
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v) $this->addOption($k, $v);
            return $this;
        }
        $this->_options[$name] = new Option($name, $spec);
        return $this;
    }

    public function addArgument($name, $spec = null)
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v) $this->addArgument($k, $v);
            return $this;
        }
        $this->_arguments[$name] = new Argument($name, $spec);
        $this->_cnt_arguments[] = $this->_arguments[$name];
        return $this;
    }

    public function options( )
    {
        if ($this->_parsed_options === false)
        {
            $this->parse();
        }
        return Nora::Hash($this->_parsed_options);
    }

    public function args( )
    {
        if ($this->_parsed_args === false)
        {
            $this->parse();
        }
        return Nora::Hash($this->_parsed_args);
    }

    public function getNextArgv( )
    {
        if ($this->_next_argv === false)
        {
            $this->parse();
        }
        $ret = $this->_next_argv;
        array_unshift(
            $ret,
            $this->getArgv()[0]
        );
        return $ret;
    }

    public function getArgv( )
    {
        return $this->_argv;
    }

    public function setArgv($argv)
    {
        $this->_argv = $argv;
        return $this;
    }


    public function parse( )
    {
        $argv = $this->getArgv();

        $this->_parsed_args = [];
        $this->_parsed_options = [];
        $this->_next_argv = [];

        $arguments = $this->_cnt_arguments;
        $arg_cnt = 0;

        for($i=1;$i<count($argv);$i++)
        {
            $arg = $argv[$i];

            $isOption = $arg{0} === '-';

            if ($isOption)
            {
                foreach($this->_options as $k=>$v) {
                    $val = null;
                    if ($v->match($arg, $val))
                    {
                        if($v->hasVal('action'))
                        {
                            $act = $v->getVal('action', true);
                            if ($act === true) {
                                $this->_parsed_options[$k] = true;
                                continue 2;
                            }

                            if ($act === '=')
                            {
                                $this->_parsed_options[$k] = $val === null ? $argv[++$i]: $val;
                                continue 2;
                            }
                        }
                    }
                }
                throw Exception\InvalidOption($arg);
            }

            if ($arg_cnt<count($arguments))
            {
                $this->_parsed_args[$arguments[$arg_cnt]->getName()] = $argv[$i];
                $arg_cnt++;
                continue;
            }

            for(;$i<count($argv);$i++)
            {
                $this->_next_argv[] = $argv[$i];
            }
        }

        return $this;
    }

    public function __toString( )
    {
        $txt = '';
        $txt.= PHP_EOL;
        $txt.= $this->_description;
        $txt.= PHP_EOL;
        $txt.= PHP_EOL;
        $txt.= 'Usage:'.PHP_EOL;
        $txt.= "\t";
        $txt.= basename($this->getArgv()[0]);
        if (count($this->_options) > 0) {
            $txt.= " [options]";
        }
        foreach($this->_arguments as $v)
        {
            $txt.= " <".$v->getName().">";
        }
        $txt.= PHP_EOL;
        if (count($this->_options) > 0) {
            $txt.= 'Options:'.PHP_EOL;
            foreach($this->_options as $v)
            {
                $txt.= "\t".$v.PHP_EOL;
            }
        }
        $txt.= 'Arguments:'.PHP_EOL;
        foreach($this->_arguments as $v)
        {
            $txt.= "\t".$v.PHP_EOL;
        }

        return $txt;
    }
}
