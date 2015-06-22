<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util\Reflection;

use ReflectionFunction as Base;

/**
 */
class ReflectionFunction extends Base
{
    use ReflectionTrait;

    /**
     * オーバーライド
     */
    public function getParameters( )
    {
        $params = [];

        foreach(parent::getParameters() as $p)
        {
            $params[] = new ReflectionParameter([
                $p->getDeclaringClass()->getName(),
                $p->getDeclaringFunction()->getName(),
            ], $p->getName());
        }
        return $params;
    }

    public function toString( )
    {
        $params = $this->getParameters();
        $param_strs = [];
        foreach($this->getParameters() as $p)
        {
            $param_strs[] = $p->toString();
        }

        $str = '';
        $str.= $this->isStatic() ? '::': '->';
        $str.= $this->getName();
        $str.= sprintf(" ( %s ) ", implode(", ", $param_strs));
        return $str;
    }

}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
