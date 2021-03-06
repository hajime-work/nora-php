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

use Nora\Base\Component\Componentable;
use Nora\Base\Environment\Environment as Base;

/**
 * Environment
 *
 * 環境へのアクセス
 */
class Environment extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
    }

    public function __invoke($client, $params)
    {
        return $this;
    }
}
