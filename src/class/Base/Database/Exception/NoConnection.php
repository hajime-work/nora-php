<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Database\Exception;
use Nora;


class NoConnection extends Base
{
    public function __construct($db, $name)
    {
        parent::__construct(Nora::message("Connection %sは定義されていません", $name));
    }
}
