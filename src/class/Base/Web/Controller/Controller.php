<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web\Controller;


use Nora\Base\Component\Componentable;
use Nora\Base\Web\Request;
use Nora\Base\Web\Response;
use Nora\Base\Web\Routing;
use Nora\Base\Web\Facade;
use Nora\Util\Reflection\ReflectionClass;
use Nora\Base\Scope;

/**
 * コントローラ
 */
class Controller implements ControllerIF
{
    use Componentable;

    /**
     * コンポーネント初期化時にコントローラ初期化メソッドを呼び出す
     */
    protected function initComponentImpl( ) {
        $this->initController( );
    }

    /**
     * 実行前のフィルタ
     */
    public function filterBefore($req, $res, $route, $facade, $method)
    {
    }

    /**
     * 実行後のフィルタ
     */
    public function filterAfter($result, $req, $res, $route, $facade, $method)
    {
        return $result;
    }

    /**
     * コントローラの初期化メソッド
     */
    protected function initController( )
    {

        // Routerコンポーネントを設定する
        $this->scope()->setComponent('Router', function ( ) {
            $router = new Routing\Router( );

            // リフレクションを読んで自動的にルータを作成する
            $rc = new ReflectionClass($this);
            foreach($rc->getMethods() as $m)
            {
                if ($m->hasAttr('route'))
                {
                    foreach($m->getAttr('route') as $v)
                    {
                        $pattern    = $v;
                        $injections = $m->getAttr('inject');

                        // 自動的にマップを作成
                        $router->map($pattern, function($req, $res, $matched, $facade) use ($m, $injections) {
                            $newRequest = clone $req;
                            $newRequest->matched()->initValues(
                                $matched->matched
                            );
                            array_push($injections, $m->getClosure($this));

                            // 実行前のフィルタ
                            $this->filterBefore($newRequest, $res, $matched, $facade, $m);

                            $result = $this->scope()->injection($injections, [$newRequest, $res, $matched, $facade]);
                            if ($result !== false)
                            {
                                return $this->filterAfter($result, $newRequest, $res, $matched, $facade, $m);
                            }
                            return false;
                        });
                    }
                }
            }
            return $router;
        });

        // 専用のファイルシステムを作成
        $this->scope()->injection([
            'FileSystem',
            'View',
            function ($fs, $view) {
                $this->scope()->setComponent('FileSystem', $fs);
            }
        ]);

        $this->initControllerImpl( );
    }

    /**
     * コントローラの初期化メソッド
     */
    protected function initControllerImpl( )
    {
    }

    /**
     * ファイルパスを取得
     */
    public function getFilePath( )
    {
        $path = implode("/", func_get_args());
        return $this->scope()->FileSystem()->getPath($path);
    }

    /**
     * スタティックに呼び出される
     *
     * @param Routing\Router $router
     * @param Routing\RouteIF $route
     * @param Facade $facade
     * @param Request\Request $req
     * @param Response\Response $res
     * @return bool
     */
    static public function run (Routing\Router $router, Routing\RouteIF $route, Facade $facade, Request\Request $req, Response\Response $res )
    {
        // マッチしたパラメタをリクエストに入れる
        $newRequest = clone $req;
        $newRequest->matched()->initValues(
            $route->matched
        );

        // 適切なスコープでクラスを作成する
        $class = get_called_class();
        $ctrl  = new $class( );
        $ctrl->setScope($facade->scope('app')->newScope($class));
        $ctrl->initComponent();

        $router = $ctrl->scope()->Router();
        $req = $newRequest;

        // ルータで処理をする
        while($route = $router->route($req))
        {
            // スペックを実行する
            $spec = $route->getSpec();

            $func = $spec[0];

            $result = call_user_func($func, $req, $res, $route, $this);

            // ディスパッチ結果がfalseであれば次のディスパッチループへ
            if (false === $result)
            {
                $this->_router->next();
                continue;
            }

            $dispatched = true;
            break;
        }

        return $dispatched === true ? true : false;
    }
}
