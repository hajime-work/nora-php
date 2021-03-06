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

use Nora;
use Nora\Scope;

/**
 * コンポーネントを管理する
 *
 * - インスタンスの生成
 * - 生成済みインスタンスの保持
 */
class ComponentLoader extends Component implements Scope\CallMethodIF
{
    protected function initComponentImpl()
    {
        $this->_namespace_list = Nora::hash();
        $this->_cash_list = Nora::hash();
        $this->_factory = Nora::hash();
        $this->_class_list = Nora::hash();
    }


    /**
     * 生成可能であればTrue
     *
     * @param stirng $name
     * @param array $params
     * @param mixed $client
     * @return bool
     */
    public function isCallable($name, $params, $client)
    {
        return $this->hasComponent($name);
    }

    public function hasComponent($name)
    {
        if (isset($this->_cash_list[$name])) return true;
        if (isset($this->_factory[$name])) return true;

        if (isset($this->_class_list[$name])) return true;

        foreach($this->_namespace_list->reverse() as $m)
        {
            $class = sprintf('%s\%s', $m, ucfirst($name));
            if (class_exists($class))
            {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 生成/もしくは取得
     *
     * @param stirng $name
     * @param array $params
     * @param mixed $client
     * @return Object
     */
    public function call($name, $params, $client)
    {
        $cmp =  $this->getComponent($name);

        if (!method_exists($cmp, '__component_invoke'))
        {
            return $cmp;
        }

        return call_user_func_array([$cmp, '__component_invoke'], [
            $client,
            $params
        ]);
    }


    /**
     * 生成
     *
     * @param stirng $name
     * @return Object
     */
    private function _create($name)
    {
        if (isset($this->_factory[$name]))
        {
            return $this->scope()->injection($this->_factory[$name]);
        }

        if (isset($this->_class_list[$name]))
        {
            $class = $this->_class_list[$name];
            return 
                $class::createComponent(
                    $this->scope()->newScope(ucfirst($name)));

        }

        foreach($this->_namespace_list->reverse() as $m)
        {
            $class = sprintf('%s\%s', $m, ucfirst($name));
            if (class_exists($class))
            {
                return $class::createComponent(
                    $this->scope()->newScope(ucfirst($name))
                );
            }
        }

        throw new Exception\ComponentNotFound($this, $name);
    }

    /**
     * 自動生成するネームスペースを追加する
     *
     * @param stirng|array $name
     * @return ComponentLoader
     */
    public function addNameSpace($string)
    {
        if (is_array($string))
        {
            foreach($string as $v) $this->addNameSpace($v);
            return $this;
        }

        $this->_namespace_list[$string] = $string;
        return $this;
    }

    /**
     * 自動生成するクラスを追加する
     *
     * @param stirng|array $name
     * @return ComponentLoader
     */
    public function addClass($name, $class = null)
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v) $this->addClass($k, $v);
            return $this;
        }

        $this->_class_list[$name] = $class;
        return $this;
    }


    /**
     * コンポーネントを設定する
     *
     * @param string $name
     * @param mixed $cb
     * @return ComponentLoader
     */
    public function setComponent($name, $cb = null)
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v)
            {
                $this->setComponent($k, $v);
            }
            return $this;
        }

        if (is_object($cb) && !($cb instanceof \Closure))
        {
            $this->_factory[$name] = function ( ) use($cb) {
                return $cb;
            };
            return $this;
        }

        $this->_factory[$name] = $cb;
        return $this;
    }

    /**
     * コンポーネントを取得する
     *
     */
    public function getComponent($name)
    {
        if (!isset($this->_cash_list[$name]))
        {
            $this->_cash_list[$name] = $this->_create($name);
        }

        $cmp =  $this->_cash_list[$name];

        return $cmp;
    }
}
