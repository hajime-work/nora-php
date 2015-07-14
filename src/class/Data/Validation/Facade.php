<?php
namespace Nora\Data\Validation;

/**
 * バリデーション
 */
class Facade extends Validator\Group
{
    public function validator( )
    {
        $v = new Validator\AllOf($this);
        $v->setup([]);
        return $v;
    }

    public function __call($name, $args)
    {
        $v = $this->validator();
        return $v->__call($name, $args);
    }
}
