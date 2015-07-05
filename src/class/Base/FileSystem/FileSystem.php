<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\FileSystem;

class FileSystem
{
    private $_root = '/';
    private $_alias_list = [];

    public function setRoot($dir)
    {
        $this->_root = $dir;
        return $this;
    }


    /**
     * エイリアスを設定する
     *
     * @string $name
     */
    public function alias($name, $path = null)
    {
        if (is_array($name)) {
            foreach($name as $k=>$v)
            {
                $this->alias($k, $v);
            }
            return $this;
        }

        $this->_alias_list[$name] = $path;
    }

    /**
     * ファイルパスを取得する
     *
     */
    public function getPath($path = null)
    {
        $path = func_get_args();
        $path = implode('/', $path);

        if ($path != null && $path{0} === '@')
        {
            if (false !== strpos($path,'/'))
            {
                list($alias,$path) = explode("/", $path, 2);
            }else{
                $alias = $path;
                $path = "";
            }

            return $this->getPath(
                $this->_alias_list[$alias].(is_null($path) ? '': "/$path")
            );
        }

        if (strlen($path) > 0 && $path[0] == '/')
        {
            return $path;
        }

        return $this->_root.'/'.ltrim($path,'/');
    }

    /**
     * ファイルリストを取得する
     */
    public function getFileList($path) 
    {
        $dir = $this->getPath($path);

        if (!is_dir($dir)) throw \Exception($dir.'はディレクトリじゃない');

        $files = glob(rtrim($dir, '/') . '/*');
        $list = array();
        foreach ($files as $file) {
            if (is_file($file)) {
                $list[] = $file;
            }
            if (is_dir($file)) {
                $list = array_merge($list, $this->getFileList($file));
            }
        }

        return $list;
    }
}
