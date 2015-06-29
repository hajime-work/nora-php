<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\View\Engine;

use Twig_LoaderInterface;

class TwigLoaderAdapter implements Twig_LoaderInterface
{
    private $_loader;

    public function __construct($loader)
    {
        $this->_loader = $loader;
    }

    public function getSource($name)
    {
        return $this->_loader->getSource($name);
    }

    public function getCacheKey($name)
    {
        return $this->_loader->getCacheKey($name);
    }

    public function isFresh($name, $time)
    {
        return $this->_loader->isFresh($name, $time);
    }
}
