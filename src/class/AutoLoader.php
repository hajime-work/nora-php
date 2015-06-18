<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */

namespace Nora;

/**
 * クラスのオートローダ
 */
class AutoLoader
{
    static $_singleton = false;

    private $_library;
    private $_maps = [];

    /**
     * オートローダを作成する
     *
     * @param array $ns_list ネームスペースをネームスペース => パスで指定する
     */
    static public function create ( $ns_list = [] )
    {
        $loader = new static();
        $loader->addLibrary($ns_list);
        return $loader;
    }

    /**
     * シングルトンで取得する
     */
    static public function singleton( $ns_list = [])
    {
        if (self::$_singleton) {
            self::$_singleton->addLibrary($ns_list);
        }else{
            self::$_singleton = self::create($ns_list);
            self::$_singleton->register();
        }
        return self::$_singleton;
    }

    /**
     * クラス名を直接マップする
     *
     * @param string $name
     * @param string $path
     */
    public function map ($name, $path)
    {
        $this->_maps[$name] = $path;
    }


    /**
     * ライブラリを追加する
     *
     * @param string|array $path
     * @param string $ns optional Namespace
     */
    public function addLibrary( $path, $ns = null )
    {
        if (is_array($path))
        {
            foreach ($path as $k=>$v)
            {
                if (is_numeric($k))
                {
                    $this->addLibrary($v);
                    continue;
                }

                $this->addLibrary($v, $k);
            }
            return $this;
        }

        $path = rtrim($path, '/');
        $this->_library[] = !is_string($ns) ? $path: ['ns' => trim($ns,'\\'), 'path' => rtrim($path,'//')];

        return $this;
    }

    /**
     * 直接呼ぶ必要なし
     *
     * @param array $array
     */
    private function __construct ( )
    {
    }


    /**
     * Autoloaderとして登録する
     *
     * @param string|array $ns
     * @param string $path
     */
    public function register ( )
    {
        spl_autoload_register([$this, 'load']);
        return $this;
    }


    /**
     * Load Class
     *
     * @param string $class
     */
    public function load ($required_class)
    {
        if (
            array_key_exists($required_class, $this->_maps) &&
            isset($this->_maps[$required_class]) &&
            file_exists($this->_maps[$required_class])
        ){
            require_once $this->_maps[$required_class];
            return true;
        }

        $logs = [];
        foreach($this->_library as $v)
        {
            $type = is_array($v) ? "ns": "lib";

            $base = false;

            if ($type == 'lib')
            {
                $path = $v;
                $class = $required_class;
            }
            elseif($type == 'ns')
            {
                $path = $v['path'];
                if (0 === strpos($required_class, $v['ns']))
                {
                    $class =  substr($required_class, strlen($v['ns']));
                }
                else
                {
                    continue;
                }
            }

            $path = $path.'/'.str_replace('\\', '/', $class).".php";

            if (file_exists($path)) {
                require_once $path;
                return true;
            }
            $logs[] = $path;
        }

        var_Dump($logs);

    }
}
