<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Scope;

use Nora\Base\Hash\Hash;
use Nora\Base\Hash\Exception\HashKeyNotExists;
use Nora\Base\Component\ComponentLoader;
use Closure;

/**
 * グローバルスコープ
 *
 * 全スコープのcallMethodとして登録される
 */
class GlobalScope extends Scope
{
    private $_componentLoader;

    /**
     * シングルトン
     *
     * @var Scope
     */
    static private $_scope = null;

    // シングルトン {{{
    public function __construct( )
    {
        parent::__construct();

        $default_helpers = [
            'AutoLoader' => function ($list) {
                \Nora\AutoLoader::singleton($list);
            }
        ];

        $this->_helpers = Hash::newHash($default_helpers, Hash::OPT_IGNORE_CASE | Hash::OPT_ALLOW_UNDEFINED_KEY_SET );

    }

    static public function getInstance( )
    {
        if ( self::$_scope === null )
        {
            self::$_scope = new GlobalScope();
        }

        return self::$_scope;
    }
    // }}}

    // For CallMethodIF {{{

    /**
     * 呼び出し可能なものリストに、Helperを追加する
     */
    public function isCallable($name, $params, $client)
    {
        if (parent::isCallable($name, $params, $client))
        {
            return true;
        }

        return $this->hasHelper($name);
    }

    /**
     * 呼び出し方法にHelperを追加する
     */
    public function call($name, $params, $client)
    {
        if (parent::isCallable($name, $params, $client))
        {
            return parent::call($name, $params, $client);
        }

        $helper = $this->getHelper($name);

        // インジェクションの場合
        if ( is_array($helper) && !is_callable($helper) && $helper[count($helper)-1] instanceof Closure)
        {
            return $client->scope()->injection($helper, $params, [
                'caller' => $client
            ]);
        }else{
            return call_user_func_array($helper, $params);
        }
    }
    // }}}

    // ヘルパー {{{

    /**
     * ヘルパーを設定する
     *
     * @param string|array $name
     * @param array|null $spec
     * @return Scope
     */
    public function setHelper($name, $spec = [])
    {
        if (is_array($name) )
        {
            array_walk($name, function ($v, $k) {
                $this->setHelper($k, $v);
            });
            return $this; 
        }

        $this->_helpers[$name] = $spec;

        return $this;
    }

    /**
     * ヘルパーがあるか
     *
     * @param string $name
     * @return bool
     */
    public function hasHelper($name)
    {
        return isset($this->_helpers[$name]);
    }

    /**
     * ヘルパーを取得
     *
     * @param string $name
     * @return callable
     */
    public function getHelper($name)
    {
        try {
            return $this->_helpers[$name];
        } catch (HashKeyNotExists $e) {

            throw new Exception\HelperNotDefined($this, $name);
        }
    }

    // }}}










}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
