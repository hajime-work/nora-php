<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Exception;

use Nora;

class ClassNotFound extends Exception
{
    public function __construct($name, $list = [])
    {
        parent::__construct(
            Nora::message("クラスが見つかりません %s in %s", [$name, var_export($list, 1)]));
    }
}
