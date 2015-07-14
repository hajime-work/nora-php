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
class Image
{
    private $_img;
    private $_ext=false;

    static public function createImageFromFile($file)
    {
        if (!file_exists($file))
        {
            throw new ImageException(Nora::Message('ファイルが存在しません'));
        }

        $func = 'imagecreatefrom'.$ext=Nora::Finfo()->getExt($file);

        $img = new Image($func($file));
        $img->_ext = $ext;
        return $img;
    }

    static public function createImageFromString($string)
    {
        return new Image($string);
    }

    public function __construct($img)
    {
        if (false === $img)
        {
            throw new ImageException(Nora::Message('有効なバイナリデータが見つかりませんでした'));
        }

        $this->_img = $img;
    }

    public function w()
    {
        return imagesx($this->_img);
    }

    public function h()
    {
        return imagesy($this->_img);
    }

    public function reduction($w, $h)
    {
        if ($this->w() > $this->h()) { // 横長だったら

            $new_width = $w;
            $new_height = $this->h() * $w/$this->w();
        }else{
            $new_height = $h;
            $new_width = $this->w() * $h/$this->h();
        }


        // 土台を作成
        $base = imagecreatetruecolor($new_width, $new_height);
        
        // リサイズ
        imagecopyresized($base, $this->_img, 0, 0, 0, 0, $new_width, $new_height, $this->w(), $this->h());

        $img =  new Image($base);
        $img->_ext = $this->_ext;
        return $img;
    }

    public function output($file = null, $type = null)
    {
        if ($type === null)
        {
            $type = $this->_ext;
        }

        $func = 'image'.$type;

        if ($file === null)
        {
            header('Content-Type: image/'. $type);
            $func($this->_img);
            return;
        }

        $func($this->_img, $file);
        return $this;
    }

    public function getExt()
    {
        return $this->_ext;
    }


    public function destroy()
    {
        unset($this->_img);
    }
}
