<?php
namespace Nora\CLI;

use Nora\Base\Component\Componentable;

/**
 * CLI:Option Parser
 */
class OptParser
{
    private $_options;
    private $_description;
    private $_env;

    public function __construct($description, $env)
    {
        $this->_description = $description;
        $this->_env = $env;
    }

    public function addOption($name, $spec)
    {
        $this->_options[$name] = $spec;
        return $this;
    }

    public function __toString( )
    {
        foreach($this->_options as $v)
        {

        }
    }
}
