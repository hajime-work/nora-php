<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Shell\Exception;
use Nora;


class ProcError extends Exception
{
    public function __construct($ret, $cmd, $msg)
    {
        parent::__construct(
            Nora::Message('[Shell-Error] %s %s %s', [
                $ret, $cmd, $msg
            ]));
    }
}
