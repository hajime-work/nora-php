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

use Nora\Base\Web\Facade as Base;

/**
 * AppのWeb用コンポーネント
 */
class Web extends Base
{
    protected function initComponentImpl( )
    {
        parent::initComponentImpl();
    }

    /**
     * WebControllerをセットアップする
     *
     * @param Nora\Component\Environment
     * @param Nora\Component\FileSystem
     * @param Nora\Component\Configure
     */
    public function initWeb($env, $fs, $conf)
    {
        $this->scope()->WebFront = $this;

        // ルータを作成する
        $router = $this->scope()->script(
            $fs->getPath('@web/routing.php')
        );

        $this->addRouter($router);
    }

    public function __invoke($client, $params = [])
    {
        return $this;
    }
}
