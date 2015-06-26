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

use Nora\Base\Web\Request;
use Nora\Base\Web\Response;
use Nora\Base\Web\Routing;
use Nora\Base\Web\Facade;

/**
 * コントローラ
 *
 */
class Controller implements ControllerIF
{

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
        return true;
    }
}
