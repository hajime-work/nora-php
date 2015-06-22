<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Component;

use Nora\Scope;

/**
 * 基礎コンポーネント
 *
 * スコープを所有する
 */
abstract class Component
{
    use Componentable;


    public function __construct( )
    {
    }


    public function __set($key, $value)
    {
        if ($this->hasScope())
        {
            $this->scope()->__set($key, $value);
            return $value;
        }
        throw Exception\ScopeNotReady('__set');
    }

    public function &__get($key)
    {
        if ($this->hasScope())
        {
            return $this->scope()->__get($key);
        }
        throw Exception\ScopeNotReady('__set');
    }

    public function __isset($key)
    {
        if ($this->hasScope())
        {
            return $this->scope()->__issset($key);
        }
        return false;
    }

    public function __call($name, $params)
    {
        if ($this->hasScope())
        {
            return $this->scope()->call($name, $params, $this);
        }

        throw Exception\ScopeNotReady('__call');
    }
}
