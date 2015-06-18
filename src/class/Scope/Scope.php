<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Scope;

use Nora\Base\Hash;

/**
 * スコープクラス
 *
 * Noraの基本となるクラス
 * スコープはスコープを持つ
 */
class Scope extends Hash\Hash implements ScopeIF,CallMethodIF
{
    private $_call_methods;

    public function __construct()
    {
        // 何でも書き込める
        $this->set_hash_option(Hash\Hash::OPT_ALLOW_UNDEFINED_KEY_SET);
        // コールメソッドを格納する
        $this->_call_methods = new Hash\ObjectHash();
        $this->setName('scope');
    }

    static public function createScope($name = 'new')
    {
        $scope = new Scope( );
        $scope->setName($name);
        return $scope;
    }


    /**
     * 読み込み専用プロパティにする
     *
     * @param string $key
     * @return Scope
     */
    public function markReadonlyProp($key)
    {
        $this->set_hash_readonly_keys([$key]);
        return $this;
    }

    /**
     * 読み込み専用プロパティを設定する
     *
     * @param string $key
     * @param mixed $value
     * @return Scope
     */
    protected function setReadonlyProp($key, $value)
    {
        $this->initValues([$key => $value]);
        $this->markReadonlyProp($key);
        return $this;
    }

    /**
     * 上書き禁止プロパティを設定する
     *
     * @param string $key
     * @param mixed $value
     * @return Scope
     */
    public function markNoOverwriteProp($key)
    {
        $this->set_hash_no_overwrite([$key]);
        return $this;
    }

    public function &__get($key)
    {
        try
        {
            return parent::__get($key);
        }catch(Hash\Exception\HashKeyNotExists $e) 
        {
            throw new Exception\UndefinedProperty($this, $key);
        }
    }

    public function __set($key, $value)
    {
        try
        {
            parent::__set($key, $value);
        }
        catch(Hash\Exception\SetOnNotAllowedKey $e) {
            throw new Exception\ReadonlyProperty($this, $key);
        }
        catch(Hash\Exception\OverwriteOnNotAllowedKey $e) {
            throw new Exception\LockedProperty($this, $key);
        }
        return $this;
    }

    /**
     * 関数のコール
     *
     * 登録されている呼び出し方法を順に試みる
     */
    public function __call($name, $params)
    {
        if ($this->isCallable($name, $params, $this))
        {
            return $this->call($name, $params, $this);
        }
        throw new Exception\CantSolvedCall($this, $name, $params);
    }

    /**
     * コールできるか
     */
    public function isCallable($name, $params, $client)
    {
        // 自分に関数が登録されているか判定する
        if( isset($this[$name]) && (is_callable($this[$name]) || $this->isInjection($this[$name])) )
        {
            return true;
        }

        // 他のコールメソッドを試す
        foreach($this->_get_scope_call_methods() as $method)
        {
            if($method->isCallable($name, $params, $this))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * インジェクションか判定
     *
     * @param array|callable $cb
     * @return bool
     */
    static function isInjection($cb)
    {
        if (is_array($cb)) {
            return is_callable($cb[count($cb)-1]);
        }
        return false;
    }

    /**
     * 機能の呼び出し
     *
     * @param string $name
     * @param array $params
     * @param object $client
     * @return mixed
     */
    public function call($name, $params, $client)
    {
        // 呼び出せるものか確認
        if (!$this->isCallable($name, $params, $client))
        {
            throw new Exception\CantSolvedCall($this, $name, $params);
        }

        // 自分に登録されているヘルパを探す
        if(isset($this[$name]) && (is_callable($this[$name])))
        {
            return call_user_func_array($this[$name], $params);
        }

        // 自分に登録されているヘルパ(インジェクション配列)を探す
        if (isset($this[$name]) && $this->isInjection($this[$name]))
        {
            if ($client instanceof ScopeIF)
            {
                return $client->injection($this[$name]);
            }else{
                return $this->injection($this[$name]);
            }
        }

        // 機能呼び出しチェーンを実行
        foreach($this->_get_scope_call_methods()->reverse() as $method)
        {
            if($method->isCallable($name, $params, $this))
            {
                // 呼び出せるチェーンから機能を呼び出す
                return $method->call($name, $params, $client);
            }
        }
        throw new \Exception('障害');
    }

    /**
     * インジェクション
     */
    public function injection($spec, $params = [])
    {
        $func = array_pop($spec);
        $args = $spec;
        $injection_params = [];

        foreach($args as $name)
        {
            $injection_params[] =  $this->resolve($name);
        }
        $injection_params += $params;

        return call_user_func_array($func, $injection_params);
    }

    /**
     * インジェクションのコンテクストを解釈
     *
     * 特別な意味を持つもの
     * 
     * scope = スコープ
     * scope:var = スコープのvar
     * scope:var() = スコープをコールした結果
     */
    public function resolve($name)
    {
        if (strtolower($name) == 'scope')  return $this;

        if (preg_match('/scope:([^\(]+)(?:(\(.*\))){0,1}/', $name, $m))
        {
            if(empty($m[2])) {
                return $this[$m[1]];
            }
            $result  = $this->call($m[1], [], $this);
            return $result;
        }

        if (isset($this[$name])) return $this[$name];

        // それ以外に該当しなければ、コールチェインへ投げる
        return $this->call($name, [], $this);
    }

    /**
     * コール用のメソッドを取得
     *
     * @return array
     */
    private function _get_scope_call_methods( ) 
    {
        return $this->_call_methods;
    }

    /**
     * コール用のメソッドを設定
     */
    public function addCallMethod(CallMethodIF $method)
    {
        $this->_call_methods->add($method);
        return $this;
    }

    /**
     * 親スコープを足す
     */
    public function setParent($object)
    {
        $this->setReadonlyProp('parent', $object);
        $this->set_hash_readonly_keys(['parent']);
    }

    /**
     * ルートスコープを取得する
     */
    public function rootScope( )
    {
        if(isset($this->parent))
        {
            return $this->parent->rootScope();
        }
        return $this;
    }


    public function setName($name)
    {
        $this->setReadonlyProp('name', $name);
    }

    public function getNames( )
    {
        if (isset($this->parent))
        {
            return $this->parent->getNames().".".$this->name;
        }else{
            return $this->name;
        }
    }

    public function newScope($name = 'child')
    {
        $scope = self::createScope($name);
        $scope->setParent($this);
        return $scope;
    }
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
