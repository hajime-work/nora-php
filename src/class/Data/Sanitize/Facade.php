<?php
namespace Nora\Data\Sanitize;

/**
 * バリデーション
 */
class Facade extends Sanitizer\Group
{
    public function sanitizer( )
    {
        $v = new Sanitizer\Group( );
        $v->setup([]);
        return $v;
    }

    public function __call($name, $args)
    {
        $v = $this->sanitizer();
        return $v->__call($name, $args);
    }
}
