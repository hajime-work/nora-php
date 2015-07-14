<?php
namespace Nora\Data\Sanitize\Sanitizer;

/**
 * サニタイザー
 */
class StripTags extends Sanitizer
{
    public function setup($args)
    {
        parent::setup($args);
    }

    public function sanitizeImpl($val)
    {
        return strip_tags($val);
    }
}
