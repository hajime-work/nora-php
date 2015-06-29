<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Database\Client\Redis;

use Nora\Base\Database\Client\Base;
use Nora\Base\Hash;
use Nora;

// https://github.com/phpredis/phpredis
use Redis;

/**
 * Redis用のFacade
 */
class Facade extends Base\Facade
{
    const REDIS_STRING=Redis::REDIS_STRING;
    const REDIS_SET=Redis::REDIS_SET;
    const REDIS_LIST=Redis::REDIS_LIST;
    const REDIS_ZSET=Redis::REDIS_ZSET;
    const REDIS_HASH=Redis::REDIS_HASH;
    const REDIS_NOT_FOUND=Redis::REDIS_NOT_FOUND;
    const DEFAULT_PORT=6379;
    const DEFAULT_TIMEOUT=1;

    private $_client;

    public function initClient($spec)
    {
        // redis->connect('127.0.0.1', 6379, 1, NULL, 100); // 1 sec timeout, 100ms delay between reconnection attempts.
        $redis = new Redis();
        $redis->connect(
            $spec['host'],
            $spec->hasVal('port') ? $spec['port']: self::DEFAULT_PORT,
            $spec->hasVal('timeout') ? $spec['timeout']: self::DEFAULT_TIMEOUT
        );
        if (isset($spec['path']))
        {
            $redis->setOption(Redis::OPT_PREFIX, $spec['path'].':');
        }
        $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);

        $this->_client = $redis;
    }

    public function status( )
    {
        var_dump($this->_client->info());
    }

    public function set($key, $value)
    {
        $this->_client->set($key, $value);
        return $this;
    }

    public function get($key)
    {
        return $this->_client->get($key);
    }

    public function has($key)
    {
        return $this->_client->exists($key);
    }


}
