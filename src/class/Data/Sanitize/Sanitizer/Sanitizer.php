<?php
namespace Nora\Data\Sanitize\Sanitizer;

/**
 * サニタイザー
 */
class Sanitizer
{
    public static function create($args)
    {
        $class = get_called_class();
        $v = new $class();
        call_user_func([$v,'setup'], $args);
        return $v;
    }

    protected function setup($args)
    {
    }

    public function sanitize($val)
    {
        if (is_null($val) || $val === '')
        {
            return null;
        }
        return $this->sanitizeImpl($val);
    }
}
