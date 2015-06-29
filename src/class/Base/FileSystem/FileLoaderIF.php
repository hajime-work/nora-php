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

interface FileLoaderIF
{
    public function getSource($name);

    public function getCacheKey($name);

    public function isFresh($name, $time);
}
