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
use Nora\Util;

use ReflectionClass;

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

    /**
     * 書き込み１度だけのプロパティを設定する
     *
     * @param string $key
     * @param mixed
     * @retusn Scope
     */
    public function setWriteOnceProp($key, $value)
    {
        $this->initValues([$key => $value]);
        $this->markNoOverwriteProp($key);
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
        if ($cb instanceof Injection\Spec)
        {
            return true;
        }
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

        // 自分に登録されているヘルパ(インジェクション配列)を探す
        if (isset($this[$name]) && $this->isInjection($this[$name]))
        {
            if ($client instanceof ScopeIF)
            {
                return $client->injection($this[$name], $params);
            }else{
                return $this->injection($this[$name], $params);
            }
        }

        // 自分に登録されているヘルパを探す
        if(isset($this[$name]) && (is_callable($this[$name])))
        {
            return call_user_func_array($this[$name], $params);
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
        if ($spec instanceof Injection\Spec)
        {
            $func = $spec->getFunction();
            $args = $spec->getSpec();
        }else{
            $func = array_pop($spec);
            $args = $spec;
        }
        $injection_params = [];

        foreach($args as $name)
        {
            $injection_params[] =  $this->resolve($name);
        }

        foreach($params as $v) {
            $injection_params[] = $v;
        }

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
     * コール用のメソッドからヘルプを作成
     */
    public function help($nest = 0)
    {
        echo str_repeat("\t", $nest);
        echo get_class($this);
        echo ' : ';
        echo $this->getNames();
        echo PHP_EOL;

        foreach($this as $k=>$v)
        {
            if ( Util\Util::isCallable($v) )
            {
                $dc = Util\Util::getDocComment($v);
                echo str_repeat("\t", $nest+1);
                echo '->';
                echo $k;
                echo '( )';
                echo ' : ';
                echo $dc->comment();
                echo PHP_EOL;
            }
        }

        /*
        foreach($this->_get_scope_call_methods() as $method)
        {
            $method->help($nest+1);
        }
         */
        return $this;
    }

    /**
     * コール用のメソッドを設定
     *
     * @return $this
     */
    public function addCallMethod(CallMethodIF $method)
    {
        $this->_call_methods->add($method);
        return $this;
    }

    /**
     * 親スコープを足す
     *
     * @return Scope
     */
    public function setParent($object)
    {
        $this->setReadonlyProp('parent', $object);
        $this->set_hash_readonly_keys(['parent']);

        // ルートスコープを自動呼び出しに参加させる
        $root = $object->rootScope();
        $this->addCallMethod($root);

        return $this;
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


    /**
     * Scopeに名前を付ける
     *
     * @param string $name
     * @return Scope
     */
    public function setName($name)
    {
        $this->setReadonlyProp('name', $name);
        return $this;
    }

    /**
     * スコープの名前を取得する(親の名前)
     *
     * @return string
     */
    public function getNames( )
    {
        if (isset($this->parent))
        {
            return $this->parent->getNames().".".$this->name;
        }else{
            return $this->name;
        }
    }

    /**
     * 新しいスクープを作成する
     *
     * @param string $name
     * @return Scope
     */
    public function newScope($name = 'child')
    {
        $scope = self::createScope($name);
        $scope->setParent($this);
        return $scope;
    }

    /**
     * オブジェクトからヘルパー群を追加する
     *
     * @param object $object
     * @return Scope
     */
    public function makeHelpers($object)
    {
        $rc = new ReflectionClass($object);
        foreach($rc->getMethods() as $m)
        {
            $dc = Util\Util::getDocComment($m);

            if ($dc->hasAttr('helper'))
            {
                foreach($dc->getAttr('helper') as $v)
                {
                    if ($v === null) $v = $m->getName();

                    // クロージャーを作成する
                    $spec = new Injection\Spec($m->getClosure($object), $dc->getAttr('inject'));
                    $this->$v = $spec;
                }
            }
        }
        return $this;
    }
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
