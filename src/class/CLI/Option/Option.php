<?php
namespace Nora\CLI\Option;

use Nora\Base\Component\Componentable;
use Nora\Base\Hash\Hash;

/**
 * CLI:Option Parser
 */
class Option extends Hash
{
    private $_name;

    public function __construct($name, $spec)
    {
        $this->_name = $name;
        $this->initValues($spec);
    }

    public function __toString( )
    {
        $txt = '';
        // $txt.= $this->_name;
        // $txt.= "\n";
        $txt.= $this->getVal('short_name');
        $this->hasVal('long_name', function($v) use (&$txt) {
            $txt.= ", $v";
        });
        $this->hasVal('description', function($v) use (&$txt) {
            $txt.= ", $v";
        });
        return $txt;
    }

    public function match($arg, &$val = null)
    {
        if ($arg{1} === '-' && false !== $p = strpos($arg, '='))
        {
            $val = substr($arg, $p+1);
            $arg = substr($arg, 0, $p);
        }

        if (in_array($arg , [$this['short_name'], $this['long_name']]))
        {
            return true;
        }
        return false;
    }
}
