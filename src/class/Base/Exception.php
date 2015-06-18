<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base;

use Exception as PHPException;

class Exception extends PHPException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
