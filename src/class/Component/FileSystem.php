<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Component;

use Nora\Base\Component\Component;
use Nora\Base\Component\Componentable;

use Nora\Base\FileSystem\FileSystem as Base;

class FileSystem extends Base
{
    use Componentable;
    
    protected function initComponentImpl( )
    {
    }

    public function __invoke($client, $params = null)
    {
        return $this;
    }
}
