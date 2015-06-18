<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora;

use Exception as PHPException;

class Exception extends PHPException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
