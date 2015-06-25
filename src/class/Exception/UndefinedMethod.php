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
use Nora\Base\Exception as Base;

class UndefinedMethod extends  Base
{
    public function __construct($obj, $method)
    {
        parent::__construct(
            Nora::message("未定義のメソッド %s->%s", [get_class($obj), $method]));
    }
}
