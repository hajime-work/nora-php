<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Scope\Exception;

use Nora\Exception as NoraException;

class Exception extends NoraException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
