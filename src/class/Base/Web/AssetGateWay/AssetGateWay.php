<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web\AssetGateWay;

use Nora\Base\Component\Componentable;
use Nora\Util;
use Nora\Base\FileSystem;
use Nora\Base\Web\Response;
use Nora\Base\Web\Exception;
use Nora\Base\Shell;

/**
 * アセット用のゲートウェイ
 *
 */
class AssetGateWay
{
    use Componentable;

    private $_paths = [];
    private $_sass_options = [];
    private $_cache;

    protected function initComponentImpl( )
    {
        $this->_fileLoader = new FileSystem\FileLoader( );

        $this->injection(['Cache', function($Cache) {
            $this->_cache = $Cache;
        }]);
    }

    /**
     * Sassへのオプションを追加する
     */
    public function setSassOptions($options)
    {
        $this->_sass_options = $options;
    }


    public function setAssetPath($path)
    {
        $this->_fileLoader->addDir($path);
    }

    /**
     * アセットをブラウザに送信する
     */
    public function send(Response\Response $output, $file)
    {
        // asset/sprite/{パス}.type
        if (basename(dirname($file)) == 'sprite')
        {
            $base = basename($file);
            $path = substr($base, 0, $p = strrpos($base, '.'));
            $type = substr($base, $p+1);

            if ($type === 'css')
            {
                return $this->sendSpriteCss($path);
            }

            return $this->sendSpriteImages($path);
        }

        if (false === $path = $this->_fileLoader->getFilePath($file))
        {
            $ext = substr($file, ($p = strrpos($file, '.'))+1);
            if (strtolower($ext) === 'css') {
                return $this->sendCss($output, substr($file, 0, $p));
            }
            if (strtolower($ext) === 'js') {
                return $this->sendJS($output, substr($file, 0, $p));
            }
            throw new Exception\AssetFileNotFound($file);
        }

        $this->sendAsset($output, $path);
    }

    public function sass ( )
    {
        $proc = new Shell\Proc('sass');
        $proc->setOptions('-s');
        $proc->setOptions($this->_sass_options);
        return $proc;
    }

    public function coffee ( )
    {
        $proc = new Shell\Proc('coffee');
        $proc->setOptions('-p');
        $proc->setOptions('-c');
        $proc->setOptions('-s');
        return $proc;
    }

    public function sendJS(Response\Response $output, $file)
    {
        foreach(['coffee'] as $ext)
        {
            $path = $this->_fileLoader->getFilePath($file.".".$ext);

            $sass = $this->coffee();
            $result = $sass->write(file_get_contents($path))->execute();

            return $output
                ->clear()
                ->header('Content-Type', 'text/javascript')
                ->cache()
                ->write($result)->send();
        }
    }

    public function sendCss(Response\Response $output, $file)
    {
        foreach(['sass', 'css'] as $ext)
        {
            if ($path = $this->_fileLoader->getFilePath($file.".".$ext))
            {
                $data = $this->_cache->asset->useCache($path, function(&$st) use ($path){
                    $sass = $this->sass();
                    $result = $sass->write(file_get_contents($path))->execute();
                    $this->logDebug('[CMD] '.$sass->build());
                    $st = true;
                    return $result;
                }, -1, filemtime($path));


                return $output
                    ->clear()
                    ->header('Content-Type', 'text/css')
                    ->cache()
                    ->write($data)->send();
            }
        }

        throw new Exception\AssetFileNotFound($file);
    }

    public function sendAsset(Response\Response $output, $path)
    {
        $File = new FileSystem\File($path);

        if ($File->getExt()  === 'php')
        {
            include $path;

        }elseif($File->getExt() === 'css' && array_key_exists('infile', $_GET)) {

            $output->header('Content-Type', $File->getMimeType())->sendHeaders();
            echo $this->imagesIntoCss($File->getContents());
        }else{

            $output->header('Content-Type', $File->getMimeType());
            $output->sendHeaders();

            $File->read();
        }
        die();
    }

    public function imagesIntoCss($string)
    {
        $mime_types = array('png'=>'image/png', 'jpg'=>'image/jpeg', 'gif'=>'image/gif');
        $regex = '/img-url\s*\((.*)\.('.implode('|',array_keys($mime_types)).')\)/im';
        echo preg_replace_callback(
            $regex,
            function ($m) use ($mime_types) {
                $url = trim($m[1], '\'\"');
                $url = preg_replace("/^\.\.\//", "", $url);

                $file = $this->_fileLoader->getFilePath($url.".".$m[2]);

                // base64を作成
                return 'url(data:'
                    .$mime_types[strtolower($m[2])]
                    .';base64,'
                    .base64_encode(file_get_contents($file))
                    .')';
                //return $m[0];
            },
            $string
        ); 
    }

    private function getSpriteInfo($path)
    {
        $list = $this->_fileLoader->getFileList($done = "img".($path === 'all' ? '': '/'.$path));
        $basePath = $this->_fileLoader->getFilePath("img");

        $types = ['png', 'gif'];

        $mxWidth = 0;
        $totalHeight = 0;
        $map = [];
        foreach($list as $img)
        {
            if(!in_array($ext = substr($img, strrpos($img, '.')+1), $types)) continue;

            // 画像サイズを取得
            list($width, $height, $type, $attr) = getimagesize($img);

            $map[] = [
                'path' => $img,
                'name' => ltrim(substr($img, strlen($basePath)), '/'),
                'top' => $totalHeight,
                'bottom' => $totalHeight + $height,
                'width' => $width,
                'height' => $height,
                'ext' => $ext,
            ];

            // 最大横幅
            $mxWidth = $mxWidth < $width? $width: $mxWidth;
            $totalHeight+= $height;
        }

        return [
            $map,
            $mxWidth,
            $totalHeight
        ];
    }

    public function sendSpriteCss($path)
    {
        list($map, $mxWidth, $totalHeight) = $this->getSpriteInfo($path);

        foreach($map as $v)
        {
            $css.= sprintf(".%s {\n", str_replace('/', '_', substr($v['name'], 0, strrpos($v['name'], '.'))));
            $css.= sprintf("background-image: url(/asset/sprite/%s);\n", $path.".png");
            //$css.= sprintf("background-size: %spx %spx;\n", $v['width'], $v['height']);
            $css.= sprintf("width: %spx;\n", $v['width']);
            $css.= sprintf("height: %spx;\n", $v['height']);
            $css.= sprintf("display: inline-block;\n");
            $css.= sprintf("background-position: 0px -%spx\n", $v['top']);
            $css.= sprintf("\n}");
        }

        header('Content-Type: text/css');
        echo $css;

    }
    public function sendSpriteImages($path)
    {
        list($map, $mxWidth, $totalHeight) = $this->getSpriteInfo($path);

        // Sprite処理をする拡張

        $im = imagecreatetruecolor($mxWidth,$totalHeight);

        //白で塗りつぶしておく
        //$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
        //imagefill($im, 0, 0, $white);

        //ブレンドモードを無効にする
        imagealphablending($resize, false);
        ////完全なアルファチャネル情報を保存するフラグをonにする
        imagesavealpha($resize, true);

        foreach($map as $v)
        {
            if ($v['ext'] === 'png') {
                $c_im = imagecreatefrompng($v['path']);
            }else{
                $c_im = imagecreatefromgif($v['path']);
            }
            imagecopy($im, $c_im, 0, $v['top'], 0, 0, $v['width'], $v['height']);
            imagedestroy($c_im);
        }
        header('Content-Type: image/png');
        imagepng($im);
        imagedestroy($im);

        die();
    }
}

