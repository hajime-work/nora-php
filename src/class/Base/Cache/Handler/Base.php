<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Cache\Handler;

use Nora\Base\Component;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * キャッシュハンドラ
 */
abstract class Base extends Component\Component
{
    private $_spec;

    public function __construct(SpecLine $spec)
    {
        $this->_spec = $spec;
    }

    protected function initComponentImpl( )
    {
        $this->initCacheHandler($this->_spec);
    }

    /**
     * UseCache
     *
     * @param string $name
     * @param callable $callback
     * @param int $expire_at
     * @param int $create_after
     */
    public function useCache ($name, $callback, $expire_at = -1, $create_after = -1)
    {
        $this->logDebug('[Cache] cache: '.$name);

        if($this->has($name, $expire_at, $create_after))
        {
            $this->logDebug('[Cache] status: found; '.$name);
            return $this->get($name);
        }

        $data = $callback($s);
        if ($s === false)
        {
            $this->logDebug('[Cache] status: fail to create; '.$name);
            return $data;
        }

        $this->set($name, $data);
        $this->logDebug('[Cache] status: created; '.$name);

        return $data;
    }

    abstract protected function initCacheHandler(SpecLine $spec);
}
