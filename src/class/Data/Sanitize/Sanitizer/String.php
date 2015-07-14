<?php
namespace Nora\Data\Sanitize\Sanitizer;

/**
 * サニタイザー
 */
class String extends Sanitizer
{
    public function setup($args)
    {
    }

    public function sanitizeImpl($val)
    {
        $tag = new StripTags();
        $html = new EscapeHtml();

        return $html->sanitize($tag->sanitize(strval($val)));
    }
}
