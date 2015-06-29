<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\View\FileLoader;


class FileLoader implements FileLoaderIF
{
    private $_dirs;

    public function __construct( )
    {
        $this->_dirs = [];
    }

    /**
     * ファイルが読めるか
     */
    public function hasSource($name)
    {
        foreach ($this->_dirs as $d)
        {
            if (file_exists($d.'/'.$name))
            {
                return true;
            }
        }
        return false;
    }

    public function getSource($name)
    {
    }

    public function getCacheKey($name)
    {
    }

    public function isFresh($name, $time)
    {
    }
}
