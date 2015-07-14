<?php
namespace Nora\Data\Sanitize\Sanitizer;

use Nora;

/**
 * サニタイザー
 */
class Image extends Group
{
    private $_opts;

    public function setup($vars)
    {
        parent::setup($vars);
        $this->_opts = Nora::Hash($vars[0]);
    }

    public function sanitizeImpl($val)
    {

        // イメージ操作を開始
        $img = Nora::Image()->createImageFromFile($val['tempfile']);

        // サイズを小さくする
        $img = $img->reduction($this->_opts['w'],$this->_opts['h'])->output($val['tempfile'].'.sanitized');

        return [
            'original' => $val,
            'filesize' => filesize($val['tempfile'].'.sanitized'),
            'tempfile' => $val['tempfile'].'.sanitized',
            'type' => $val['type'],
            'ext' => $img->getExt()
        ];
    }
}
