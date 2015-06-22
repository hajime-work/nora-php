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
 * 基礎コンポーネント用のTrait
 */
trait Componentable
{
    private $_scope;

    /**
     * ヘルプを表示する
     *
     * 該当コンポーネントの使い方を出したい
     */
    public function help( )
    {
        $this->scope()->help();
    }

    /**
     * 自分用のスコープをセットする
     *
     * @param Scope $scope
     * @return void
     *
     */
    public function setScope(Scope\ScopeIF $scope)
    {
        $scope->setWriteOnceProp('owner', $this);
        $this->_scope = $scope;
    }

    /**
     * スコープがあるか
     *
     * @return bool
     */
    public function hasScope( )
    {
        return !empty($this->_scope);
    }
    
    /**
     * スコープを取得する
     *
     * @return Scope
     */
    public function scope( )
    {
        return $this->_scope;
    }

    /**
     * 新しいインスタンスを作成する
     *
     * @param Scope $scope
     * @return Component
     */
    static public function createComponent(Scope\ScopeIF $scope)
    {
        $class = get_called_class();
        $comp = new $class();
        $comp->initComponent($scope);
        return $comp;
    }

    /**
     * コンポーネントを初期化する
     *
     * @param Scope scope
     * @return void
     */
    public function initComponent(Scope\ScopeIF $scope = null)
    {
        if ($scope instanceof Scope\ScopeIF)
        {
            $this->setScope($scope);
        }
        $this->initComponentImpl();
    }

    abstract protected function initComponentImpl( );
}
