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
use Nora\Base\Configuration\Configure as Base;

/**
 * 基礎コンポーネント
 *
 * 設定値を保持するオブジェクト
 */
class Configure
{
    use Componentable;

    protected function initComponentImpl( )
    {
    }

    public function __invoke($client, $params)
    {
        if (!isset($client->configure))
        {
            $client->scope()->setWriteOnceProp('configure', new Base());
        }

        if (empty($params))
        {
            return $client->configure;
        }

        return call_user_func_array([$client->configure, 'read'], $params);
    }
}
