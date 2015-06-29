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

/**
 * アセット用のゲートウェイ
 *
 */
class AssetGateWay
{
    use Componentable;

    private $_paths = [];

    protected function initComponentImpl( )
    {
        $this->_fileLoader = new FileSystem\FileLoader( );
    }

    public function setAssetPath($path)
    {
        $this->_fileLoader->addDir($path);
    }

    public function send(Response\Response $output, $file)
    {
        if (false === $path = $this->_fileLoader->getFilePath($file))
        {
            throw new Exception\AssetFileNotFound($file);
        }

        $this->sendAsset($output, $path);
    }

    public function sendAsset(Response\Response $output, $path)
    {
        $File = new FileSystem\File($path);

        $output->header('Content-Type', $File->getMimeType());
        $output->sendHeaders();

        $File->read();
        die();
    }
}

