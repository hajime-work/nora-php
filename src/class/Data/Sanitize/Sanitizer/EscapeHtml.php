<?php
namespace Nora\Data\Sanitize\Sanitizer;

/**
 * サニタイザー
 */
class EscapeHtml extends Sanitizer
{
    public function setup($args)
    {
        parent::setup($args);
    }

    public function sanitize($val)
    {
        return htmlentities(trim($val), ENT_NOQUOTES);
    }
}
