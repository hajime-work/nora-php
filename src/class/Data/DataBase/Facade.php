<?php
namespace Nora\Data\DataBase;

use Nora\Base\Component;
use Nora\Util\Util;
use Nora;

/**
 * データベース
 */
class Facade extends Component\Component
{
    private $_specs;
    private $_loader;

    public function __construct( )
    {
        // 擬似シングルトン用のローダ
        $this->_loader = Util::instanceLoader(function($key) {
            return $this->connect($this->_specs[$key]);
        });

        $this->_specs = Nora::Hash();
    }

    protected function initComponentImpl( )
    {
    }

    /**
     * 接続する
     */
    public function connect($spec)
    {
        if (!($spec instanceof Connection\Spec))
        {
            $spec = new Connection\Spec($spec);
        }

        $class = sprintf(__namespace__.'\\Client\\%s\\Facade', ucfirst($spec->scheme));
        return new $class($spec);
    }

    /**
     * 接続先を登録
     */
    public function setConnection($name, $spec = null)
    {
        if (is_array($name))
        {
            foreach($name as $k=>$v) $this->setConnection($k, $v);
            return $this;
        }

        if (!($spec instanceof Connection\Spec))
        {
            $spec = new Connection\Spec($spec);
        }

        $this->_specs[$name] = $spec;
        return $this;
    }


    public function getConnection($name)
    {
        return $this->_loader->get($name);
    }

    public function hasConnection($name)
    {
        return isset($this->_specs[$name]);
    }

    public function __invoke($name)
    {
        return 
            $this->getConnection($name);
    }
}
