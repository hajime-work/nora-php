<?php
namespace Nora\CLI\Option;

use Nora\Base\Component\Componentable;
use Nora\Base\Hash\Hash;

/**
 * CLI:Option Argument
 */
class Argument extends Hash
{
    private $_name;

    public function __construct($name, $spec)
    {
        $this->_name = $name;
        $this->initValues($spec);
    }

    public function getName()
    {
        return $this->_name;
    }

    public function __toString( )
    {
        $txt = '';
        $txt.= sprintf('%s', $this->_name);
        $this->hasVal('description', function($v) use (&$txt) {
            $txt.= " $v";
        });
        return $txt;
    }
}
