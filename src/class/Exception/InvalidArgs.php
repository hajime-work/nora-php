<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Exception;

use Nora;

class InvalidArgs extends Exception
{
    public function __construct($class, $method, $args)
    {
        parent::__construct(
            Nora::message("引数に誤りがあります %s::%s  %s", [$class, $method, var_export($args, 1)]));
    }
}
