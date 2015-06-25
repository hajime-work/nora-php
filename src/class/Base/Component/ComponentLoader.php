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

class ComponentLoader extends Component implements Scope\CallMethodIF
{
    protected function initComponentImpl()
    {
        $this->_namespace_list = Nora::hash();
        $this->_cash_list = Nora::hash();
        $this->_factory = Nora::hash();
    }


    public function isCallable($name, $value, $client)
    {
        if (isset($this->_cash_list[$name])) return true;
        if (isset($this->_factory[$name])) return true;

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

    public function call($name, $params, $client)
    {
        if (!isset($this->_cash_list[$name]))
        {
            $this->_cash_list[$name] = $this->_create($name);
        }

        $cmp =  $this->_cash_list[$name];
        if (!is_callable($cmp))
        {
            return $cmp;
        }

        return call_user_func_array($cmp, [
            $client,
            $params
        ]);
    }


    private function _create($name)
    {
        if (isset($this->_factory[$name]))
        {
            return $this->scope()->injection($this->_factory[$name]);
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

        throw new Exception\ComponentNotFound($name);
    }

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

        $this->_factory[$name] = $cb;
        return $this;
    }
}
