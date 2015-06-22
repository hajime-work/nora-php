<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Configuration;

/**
 * 設定管理ライブラリ
 */
class Configure extends ItemAggregate
{
    private $_loaded_files = [];

    /**
     * 設定をディレクトリから読む
     *
     * @param array $dir
     * @param string $name
     * @return Configure
     */
    public function load($dir, $name)
    {
        // 複数を同時に読む
        if (func_num_args() > 2) {
            $args = func_get_args( );

            for($i=1; $i<count($args); $i++) $this->load($dir, $args[$i]);
            return $this;
        }

        if (is_array($name)) {
            foreach($name as $v) $this->load($dir, $v);
            return $this;
        }

        if (is_array($dir)) {
            foreach($dir as $d) $this->load($d, $name);
            return $this;
        }


        $file = $dir.'/'.$name.'.php';
        if (file_exists($file)) {
            $this->loadFile($file);
        }

        $files = $dir.'/'.$name.'/*.php';

        foreach(glob($files) as $file)
        {
            $name = basename($file);
            $name = substr($name, 0, strpos($name, '.'));
            $this->loadFile($file, $name);
        }

        return $this;
    }

    private function loadFile($file, $name = null)
    {
        if (in_array($file, $this->_loaded_files))
        {
            return;
        }

        if (!file_exists($file)) throw new Exception\CantLoadFile($file);

        $array = include $file;
        if ($name !== null) $this->write($name, $array);
        else $this->write($array);

        $this->_loaded_files[] = $file;
    }

    public function __invoke($key, $value = null)
    {
        return $this->read($key, $value);
    }

    public function save($path)
    {
        $array = $this->toArray();
        return file_put_contents($path, serialize($array));
    }

    /*
    protected function initComponentImpl ( )
    {
    }

    static public function createConfigure( )
    {
        return new Configure();
    }

    public function walk($getter, $callback)
    {
        call_user_func_array(
            $callback,
            $this->getVars($getter)
        );
        return $this;
    }

    public function getVars($getter)
    {
        $vars = [];

        foreach($getter as $k=>$v)
        {
            if (is_numeric($k)) {
                $k = $v;
                $v = null;
            }

            $ret = $this->read($k, $v);
            if (is_array($v) && is_object($ret))
            {
                $ret = $ret->toArray();
            }elseif (is_array($v) && !is_array($ret))
            {
                $ret = [$ret];
            }
            $vars[$k] = $ret;
        }
        return $vars;
    }

    public function load($dir, $name)
    {
        if (func_num_args() > 2)
        {
            $args = func_get_args( );

            for($i=1; $i<count($args); $i++)
            {
                $this->load($dir, $args[$i]);
            }
            return $this;
        }

        if (is_array($name))
        {
            foreach($name as $v)
            {
                $this->load($dir, $v);
            }
            return $this;
        }

        if (is_array($dir))
        {
            foreach($dir as $d)
            {
                $this->load($d, $name);
            }
            return $this;
        }

        $file = $dir.'/'.$name.'.php';

        if (file_exists($file)) {
            $this->loadFile($file);
        }

        $files = $dir.'/'.$name.'/*.php';

        foreach(glob($files) as $file)
        {
            $name = basename($file);
            $name = substr($name, 0, strpos($name, '.'));
            $this->loadFile($file, $name);
        }

        return $this;
    }

    private function loadFile($file, $name = null)
    {
        if (!file_exists($file))
        {
            throw new ApplicationError(
                sprintf(
                    __('%sファイルは存在しません'),
                    $file
                )
            );
        }

        $array = include $file;

        if ($name !== null) {
            $this->write($name, $array);
        }
        else{
            $this->write($array);
        }
    }

    public function __invoke($key, $value = null)
    {
        return $this->read($key, $value);
    }

    public function save($path)
    {
        $array = $this->toArray();
        return file_put_contents($path, serialize($array));
    }

     */
}
