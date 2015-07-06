<?php
namespace Nora\Data\DataBase\Client\Redis;

use Nora\Data\DataBase\Client\Base;
use Nora;

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
    const DEFAULT_TIMEOUT=2;

    protected function initClient($spec)
    {
        // redis->connect('127.0.0.1', 6379, 1, NULL, 100); // 1 sec timeout, 100ms delay between reconnection attempts.
        $redis = new Redis();
        $redis->connect(
            $spec->host,
            $spec->get('port', self::DEFAULT_PORT),
            $spec->getAttr('timeout', self::DEFAULT_TIMEOUT)
        );
        if ($spec->has('field'))
        {
            $redis->setOption(Redis::OPT_PREFIX, $spec->get('field'));
        }
        $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);

        $this->setConnection($redis);
    }

    public function status( )
    {
        var_dump($this->con()->info());
    }

    public function set($key, $value)
    {
        $this->con()->set($key, $value);
        return $this;
    }

    public function get($key)
    {
        return $this->con()->get($key);
    }

    public function has($key)
    {
        return $this->con()->exists($key);
    }

    public function delete($key)
    {
        $this->con()->delete($key);
        return $this;
    }

}
