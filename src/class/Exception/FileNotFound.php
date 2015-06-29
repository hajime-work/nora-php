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

class FileNotFound extends Exception
{
    public function __construct($name, $list = [])
    {
        parent::__construct(
            Nora::message("ファイルが見つかりません %s in %s", [$name, var_export($list, 1)]));
    }
}
