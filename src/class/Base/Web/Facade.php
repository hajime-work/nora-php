<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web;

use Nora\Base\Component\Componentable;
use Nora;

/**
 * Web Facade
 *
 */
class Facade
{
    use Componentable;

    private $_router;

    protected function initComponentImpl( )
    {
        $this->_router = new Routing\Router();

        $this->scope()->injection([
            'Environment',
            'FileSystem',
            'Configure',
            function ($e, $f, $c)
            {
                $f->alias([
                    '@web' => 'web'
                ]);

                $this->initWeb($e, $f, $c);
            }
        ]);

    }

    /**
     * ルーターを作成する
     *
     * @return Nora\Base\Web\Routing\Router
     */
    public function newRouter ( )
    {
        return new Routing\Router( );
    }

    /**
     * ルータを追加する
     *
     * @return Facade
     */
    public function addRouter(Routing\Router $rt)
    {
        $this->_router->addRouter($rt);
    }


    /**
     * Webをスタートする
     */
    public function run ( )
    {
        // Dispatchしたか
        $dispatched = false;

        // リクエストを取得する
        $req = new Request\Request( );
        $res = new Response\Response();

        // ルータで処理をする
        while($route = $this->_router->route($req))
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

        if ($dispatched === true)
        {
            // レスポンスを描画する
            return $res->send();
        }

        // 404 not found
        $this->notfound(Nora::Message("%sは見つかりませんでした。", $req), "Page Not Found", 404);
    }

    /**
     * NotFound処理
     */
    public function notfound($message, $title = 'Not Found', $status = 404)
    {
        $res = new Response\Response();
        return $res
            ->status($status)
            ->write(
                sprintf(
                    "<html>\n<body>\n<h4>%s</h4>\n<quote>%s</quote>\n</body>\n</html>",
                    $title,
                    $message
                )
            )
            ->send();
    }
}
