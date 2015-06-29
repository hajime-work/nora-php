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

    public function setSassOptions($options)
    {
        $this->_sass_options = $options;
    }

    public function sass ( )
    {
        $proc = new Shell\Proc('sass');
        $proc->setOptions('-s');
        $proc->setOptions($this->_sass_options);
        return $proc;
    }

    public function setAssetPath($path)
    {
        $this->_fileLoader->addDir($path);
    }

    public function send(Response\Response $output, $file)
    {
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
        }else{

            $output->header('Content-Type', $File->getMimeType());
            $output->sendHeaders();

            $File->read();
        }
        die();
    }
}

