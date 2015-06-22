<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util\Reflection\Exception;

use Nora\Base\Exception;


/**
 * DocCommentを制御する
 */
class CantRetriveDocComment extends Exception
{
    public function __construct($object)
    {
        parent::__construct(gettype($object));
    }
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
