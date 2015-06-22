<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util\Reflection;

use ReflectionParameter as Base;

/**
 * ユーティリティファサード
 */
class ReflectionParameter extends Base
{
    use ReflectionTrait;


    public function toString( )
    {
        $str = '';

        // 参照渡しか
        if ( $this->isPassedByReference() )
        {
            $str.= '&';
        }
        $str.= '$';

        $str.= $this->getName();
    
        // 初期値付き
        if ($this->isOptional())
        {
            $str.='=';

            $param_val = $this->getDefaultValueConstantName();
            if (empty($param_val))
            {
                $param_val = $this->getDefaultValue();
            }
            if (empty($param_val))
            {
                $param_val = 'NULL';
            }
            $str.=$param_val;
        }

        return $str;
    }
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
