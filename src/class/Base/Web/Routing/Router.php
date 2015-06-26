<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Web\Routing;

use Nora\Base\Web\Controller;
use Nora\Base\Web\Request;
use Nora\Base\Hash\ObjectHash;
use Nora\Util\Util;

/**
 * ルーティングクラス
 *
 */
class Router implements RouteIF
{
    private $_nss;
    private $_routes;

    public function __construct()
    {
        $this->_nss= [];
        $this->_routes = new Routes( );
    }

    public function routes()
    {
        return $this->_routes;
    }

    /**
     * ネームスペースを追加する
     *
     * @param string $name
     */
    public function appendNamespace($name)
    {
        if (is_array($name)) {
            array_walk($name, function($v) { $this->appendNamespace($v); });
            return $this;
        }
        
        $this->_nss[] = $name;
        return $this;
    }

    /**
     * コントローラをURLに関連付ける
     *
     * @param string $name
     * @param string $url
     * @return Router
     */
    public function addController($name, $url)
    {
        $class = Util::findClassName($name, $this->_nss);

        $this->map($url, $this->makeRouteClosure($class));
        return $this;
    }

    /**
     * ルータを追加する
     *
     * @param Router $router
     * @return Router
     */
    public function addRouter(Router $router)
    {
        $this->addRoute($router);
        return $this;
    }

    /**
     * ルートを追加する
     *
     * @param RouteIF $rt
     * @return Router
     */
    public function addRoute(RouteIF $rt)
    {
        $this->_routes->add($rt);
        return $this;
    }

    private function makeRouteClosure($class)
    {
        return function ($req, $res, $facade) use ($class) {
            return $this->called($class, $req, $res, $facade);
        };
    }

    private function called($class, $req, $res, $facade)
    {
        //var_Dump($class);
    }

    /**
     * ルーティングを実行する
     */
    public function route(Request\Request $req)
    {
        while($rt = $this->_routes->current())
        {
            if ($matched = $rt->match($req))
            {
                return $matched;
            }
            $this->_routes->next();
        }
        return false;
    }

    public function next( )
    {
        $this->_routes->next();
    }

    /**
     * マッチをさせる
     */
    public function match(Request\Request $req)
    {
        return $this->route($req);
    }


    /**
     * 複数のコントローラをURLに関連付ける
     *
     * @param array $list
     * @param mixed $spec
     * @return Router
     */
    public function addControllers($list)
    {
        foreach($list as $k=>$v) $this->addController($k, $v);
        return $this;
    }

    /**
     * URLに関数を関連付ける
     * 
     * @param string $url
     * @param mixed $spec
     * @return Router
     */
    public function map($url, $spec)
    {
        $this->_routes->add( new Route(func_get_args()));
        return $this;
    }

}