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
use Nora\Base\Logging\LogLevel;
use Nora\Base\Event;

/**
 * 基礎コンポーネント用のTrait
 */
trait Componentable
{
    use Event\SubjectTrait;

    private $_scope;

    abstract protected function initComponentImpl( );

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
    protected function setScope(Scope\ScopeIF $scope)
    {
        $scope->setWriteOnceProp('owner', $this);
        $this->_scope = $scope;
    }

    /**
     * スコープがあるか
     *
     * @return bool
     */
    protected function hasScope( )
    {
        return !empty($this->_scope);
    }
    
    /**
     * スコープを取得する
     *
     * @return Scope
     */
    public function scope($tag = null, $cnt = 0)
    {

        if ($tag !== null)
        {
            return $this->scope()->find($tag, $cnt);
        }

        if (!isset($this->_scope))
        {
            $this->_scope = new Scope\Scope();
        }
        return $this->_scope;
    }

    // スコープへ処理を移譲するメソッド {{{

    /**
     * スコープに解決してもらう
     */
    public function resolve($name)
    {
        return $this->scope()->resolve($name);
    }

    /**
     * ルートスコープを取得する
     */
    public function rootScope()
    {
        return $this->scope()->rootScope();
    }

    /**
     * グローバルスコープを取得する
     */
    public function globalScope( )
    {
        return $this->scope()->globalScope();
    }

    public function injection($spec, $params = [], $client = null)
    {
        if ($client === null) $client = $this;

        return $this->scope()->injection($spec, $params, $client);
    }

    // }}}

    // ロギング {{{
    
    public function logEmerg($message)
    {
        $this->log(LogLevel::EMERG, $message);
    }

    public function logAlert($message)
    {
        $this->log(LogLevel::ALERT, $message);
    }

    public function logCrig($message)
    {
        $this->log(LogLevel::CRIT, $message);
    }
    public function logErr($message)
    {
        $this->log(LogLevel::ERR, $message);
    }
    public function logWarning($message)
    {
        $this->log(LogLevel::WARNING, $message);
    }
    public function logNotice($message)
    {
        $this->log(LogLevel::NOTICE, $message);
    }
    public function logInfo($message)
    {
        $this->log(LogLevel::INFO, $message);
    }
    public function logDebug($message)
    {
        $this->log(LogLevel::DEBUG, $message);
    }

    /**
     * ログ処理
     *
     * ルートスコープのイベントにログイベントを投げ込む
     *
     * @param int $level
     * @param mixed $message
     * @return void
     */
    private function log ($level, $message)
    {
        $this->rootScope()->fire(
            'log',
            [
                'level' => $level,
                'message' => is_String($message) ? ['msg' => $message]: $message,
                'tags' => [
                    $this->scope()->getNames()
                ],
                'contect' => $this
            ]
        );
    }

    // }}}

    public function __component_invoke($client, $params)
    {
        if (empty($params))
        {
            return $this;
        }


        if (!method_exists($this, '__invoke')) return $this;

        return call_user_func_array([$this, '__invoke'], $params);
    }

}

/* vim: set ft=php foldmethod=marker: */
