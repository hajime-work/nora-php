<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Data;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * データ:ハンドリング
 *
 */
class Facade extends Component\Component
{
    private $_ds_list;
    private $_ns_list;

    protected function initComponentImpl( )
    {
        $this->_ds_list = Nora::hash();
        $this->_ns_list = Nora::hash();
    }

    public function getDataHandler($name)
    {
        $class = false;
        foreach($this->_ns_list as $ns)
        {
            $cand = $ns.'\\'.ucfirst($name);
            if (class_exists($cand))
            {
                $class = $cand;
            }
        }

        if ($class === false) {
            $class = __namespace__.'\\Base\\DataHandler';
        }

        $comp = $class::createComponent($this->scope()->newScope($name));

        if ($this->_ds_list->hasVal($name))
        {
            $spec = $this->_ds_list->getVal($name);
            $comp->setStorage($spec->scheme());
            $comp->setTable($spec->host());
            return $comp;
        }else{
            $this->logNotice('Dosenot Have '.$name.' in $_ds_list');
        }


        return $comp;
    }

    public function __invoke($name)
    {
        return $this->getDataHandler($name);
    }

    public function addDataSource($name, $value = null)
    {
        if (is_array($name)) {
            foreach($name as $k=>$v) $this->addDataSource($k, $v);
            return $this;
        }

        $spec = new SpecLine($value);
        $this->_ds_list->setVal($name, $spec);
        return $this;
    }

    public function addNameSpace($name)
    {
        if (is_array($name)) {
            foreach($name as $v) $this->addNameSpace($v);
            return $this;
        }
        $this->_ns_list[$name] = $name;
        return $this;
    }

}
