<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util\Reflection;

use ReflectionClass as Base;

class ReflectionClass extends Base
{
    use ReflectionTrait;

    /**
     * オーバーライド
     */
    public function getMethods($filter = null)
    {
        $methods = [];
        foreach(parent::getMethods() as $m)
        {
            $methods[] = new ReflectionMethod($this->getName(), $m->getName());
        }
        return $methods;
    }

    /**
     * パブリックメソッドだけを取得する
     *
     */
    public function getPublicMethods( )
    {
        $methods = [];
        foreach(parent::getMethods() as $m)
        {
            if ($m->isPublic())
            {
                $methods[] = new ReflectionMethod($this->getName(), $m->getName());
            }
        }
        return $methods;
    }
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
