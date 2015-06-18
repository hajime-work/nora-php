<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Hash\Exception;
use Nora\Base\Exception;
use Nora\Base\Hash\HashIF;
use Nora\Nora;


class InvalidCallback extends Exception
{
    public function __construct(HashIF $hash)
    {
        parent::__construct(Nora::message('コールバック関数ではありません'));
    }
}
