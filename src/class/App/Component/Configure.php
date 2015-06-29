<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\App\Component;

use Nora\Base\Component\Componentable;
use Nora\Base\Configuration\Configure as Base;

/**
 * App用のコンポーネント
 *
 * Nora->App->ComponentLoader->Confiure
 */
class Configure extends Base
{
    use Componentable;

    protected function initComponentImpl( )
    {
        // Appの親スコープが所有するConfigureを取得
        //
        // Nora->App->ComponentLoader->Confiure
        //
        //       (app) <---------------------------
        //  <----
        //
        $parent = $this
            ->scope('app')
            ->getParent()
            ->Configure();

        $this
            ->write(
                $parent->toArray() // 上位の設定値を書き込み
            )
            ->write(
                $this->scope('app')->appConfig  // App登録時に取得しておいた設定値をリストア
            );
    }

    public function __invoke($client, $params =[])
    {
        if (empty($params))
        {
            return $this;
        }

        return call_user_func_array([$this,'read'], $params);
    }
}
