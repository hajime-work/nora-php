<?php
namespace Nora\Data\Sanitize\Sanitizer;

/**
 * サニタイザー
 */
class Int extends Sanitizer
{

    public function sanitizeImpl($val)
    {
        return intval($val);
    }
}
