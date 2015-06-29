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

class FileLoader implements FileLoaderIF
{
    private $_dirs = [];

    public function __construct($dirs = [])
    {
        $this->addDir($dirs);
    }

    public function addDir($dir)
    {
        if (is_array($dir))
        {
            array_walk($dir, function($v) { $this->addDir($v); });
            return $this;
        }

        array_unshift( $this->_dirs, $dir );
        return $this;
    }

    public function getFilePath($name)
    {
        if (file_exists($name))
        {
            return $name;
        }

        if (false === $p = strrpos($name, '.'))
        {
            foreach(['twig', 'html'] as $v)
            {
                if ($path =  $this->getFilePath( $name.".".$v))
                {
                    return $path;
                }
            }
        }

        foreach ($this->_dirs as $d)
        {
            if (file_exists($d.'/'.$name))
            {
                return $d.'/'.$name;
            }
        }
        return false;
    }


    /**
     * ファイルが読めるか
     */
    public function hasSource($name)
    {
        return $this->getFilePath($name) === false ? false: $name;
    }

    public function getSource($name)
    {
        if ($path = $this->getFilePath($name))
        {
            return file_get_contents($path);
        }

        throw new FileNotFound($name, ['/']);
    }

    public function getCacheKey($name)
    {
        return $this->getFilePath($name);
    }

    public function isFresh($name, $time)
    {
        return $time < fileatime($this->getFilePath($name));
    }
}
