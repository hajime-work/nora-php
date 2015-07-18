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

        return $html->sanitize($tag->sanitize(strval($val)));
    }
}
