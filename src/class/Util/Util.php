<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util;
use Nora\Scope\Injection;

/**
 * ユーティリティファサード
 */
class Util
{
    
    /**
     * DocCommentを取得する
     *
     * @param mixed $object
     * @return DocComment
     */
    static public function getDocComment($object)
    {
        return Reflection\ReflectionDocComment::create($object);
    }

    /**
     * コーラブル
     *
     * @param mixed $v
     * @return bool
     */
    static public function isCallable($v)
    {
        return is_callable($v) || (is_array($v) && is_callable($v[count($v)-1]));
    }
    
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
