<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\FileSystem;

use Nora\Exception\FileNotFound;

class File
{
    static private $_mime_map = [
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'js'   => 'text/javascript',
        'php'  => 'application/x-httpd-php',
        'css'  => 'text/css',
        'html' => 'text/html'
    ];

    public function __construct($path)
    {
        if (!file_exists($path))
        {
            throw new FileNotFound($path, []);
        }

        $this->_path = $path;
    }

    public function getPath( )
    {
        return $this->_path;
    }

    public function getExt( )
    {
        if (false !== $p = strrpos($this->getPath(),'.'))
        {
            return substr($this->getPath(), $p+1);
        }

        return false;
    }

    public function getMimeType( )
    {
        if ((false !== $ext = $this->getExt()) && isset(self::$_mime_map[$ext]))
        {
            return self::$_mime_map[$ext];
        }

        throw new \Exception("$ext は登録されていません");
    }

    public function read( )
    {
        readfile($this->getPath());
    }

    public function getContents( )
    {
        return file_get_contents($this->getPath());
    }
}
