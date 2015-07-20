<?php
namespace Nora\Data\Sanitize\Sanitizer;

/**
 * サニタイザー
 */
class Url extends Sanitizer
{
    public function setup($args)
    {
    }

    public function sanitizeImpl($val)
    {
        $tag = new StripTags();

        return $tag->sanitize(strval($val));
    }
}
