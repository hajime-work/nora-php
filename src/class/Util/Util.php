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

use ReflectionMethod;
use ReflectionFunction;
use ReflectionClass;

/**
 * ユーティリティファサード
 */
class Util
{
    /**
     * DocCommentを取得する
     *
     * @param mixed $object
     * @return string 
     */
    static public function getDocCommentRaw($object)
    {
        if ($object instanceof ReflectionMethod || $object instanceof Injection\Spec)
        {
            return $object->getDocComment();
        }

        // オブジェクトを判定
        if ( is_callable($object) && is_array($object) )
        {
            $rs = new ReflectionMethod($object[0], $object[1]);
            return $rs->getDocComment();
        }

        if (is_callable($object))
        {
            $rs = new ReflectionFunction($object);
            return $rs->getDocComment();
        }

        if (is_array($object) && is_callable($object[count($object)-1]))
        {
            return self::getDocCommentRaw($object[count($object)-1]);
        }

        if (is_object($object))
        {
            $rs = new ReflectionClass($object);
            return $rs->getDocComment();
        }

        return false;
    }
    
    /**
     * DocCommentを取得する
     *
     * @param mixed $object
     * @return DocComment
     */
    static public function getDocComment($object)
    {
        $text = self::getDocCommentRaw($object);
        return DocComment::create($text);
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
