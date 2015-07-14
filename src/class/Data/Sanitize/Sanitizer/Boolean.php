<?php
namespace Nora\Data\Sanitize\Sanitizer;

/**
 * サニタイザー
 */
class Boolean extends Sanitizer
{
    public function setup($args)
    {
    }

    public function sanitize($val)
    {
        return boolval($val);
    }
}
