<?php
namespace Nora\Data\Sanitize\Sanitizer;

use Nora;

/**
 * サニタイザー
 */
class ImageToBase64 extends Group
{
    public function setup($vars)
    {
        parent::setup($vars);
    }

    public function sanitizeImpl($val)
    {
        // イメージをBase64にする
        $img = sprintf('data: image/%s; base64,%s',
            $val['ext'],
            base64_encode(file_get_contents($val['tempfile']))
        );
        return $img;
    }
}
