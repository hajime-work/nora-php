<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Scope\Injection;

use Nora\Base\Hash;
use Nora\Util;

use ReflectionClass;

/**
 * Injection Spec
 */
class Spec
{
    private $_function;

    public function __construct($function, $injection_spec =[], $overwrite = [])
    {
        $this->_function = $function;
        $this->_injection_spec = $injection_spec;
        $this->_overwrite = $overwrite;
    }

    public function getDocComment( )
    {
        return Util\Util::getDocComment($this->_function);
    }

    public function getFunction()
    {
        return $this->_function;
    }

    public function getSpec()
    {
        return $this->_injection_spec;
    }

    public function getOverwrite()
    {
        return $this->_overwrite;
    }

    public function execute($params)
    {
        return call_user_func_array(
            $this->getFunction(),
            $params
        );
    }

    public function __invoke()
    {
        return $this->execute(func_get_args());
    }
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */

