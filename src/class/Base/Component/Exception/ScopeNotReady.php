<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Component\Exception;
use Nora\Base\Exception;
use Nora;


class ScopeNotReady extends Exception
{
    public function __construct($comp, $type, $params)
    {
        parent::__construct(Nora::message('スコープがセットされていません %s, %s, %s', [
            get_class($comp),
            $type,
            var_export($params,1)
        ]));
    }
}
