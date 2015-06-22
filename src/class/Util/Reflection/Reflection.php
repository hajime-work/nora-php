<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util\Reflection;

use Closure;

class Reflection
{
    /**
     * DocCommentを取得する
     *
     * @param mixed $object
     * @return string 
     */
    static public function getReflection($object)
    {
        if ($object instanceof ReflectionMethod || $object instanceof Injection\Spec)
        {
            return $object;
        }

        // オブジェクトを判定
        if ( is_callable($object) && is_array($object) )
        {
            return new ReflectionMethod($object[0], $object[1]);
        }

        if ($object instanceof Closure)
        {
            return new ReflectionFunction($object);
        }

        if (is_object($object))
        {
            return new ReflectionClass($object);
        }

        throw new Exception\CantRetriveDocComment($object);
    }
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
