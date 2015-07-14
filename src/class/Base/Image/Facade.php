<?php
namespace Nora\Base\Image;

use Nora\Base\Component\Componentable;
use Nora;

/**
 * Image
 * ======
 *
 * 画像処理系
 *
 */
class Facade
{
    use Componentable;

    private $_finfo;

    protected function initComponentImpl( )
    {
        $this->injection([
            'Finfo', function($finfo) {
                $this->_finfo = $finfo;
            }
        ]);
    }


    public function createImageFromFile($file)
    {
        return Image::createImageFromFile($file);
    }

}
