<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Util;
use Nora\Scope\Injection;
use Nora\Exception;
use finfo;

/**
 * ユーティリティファサード
 */
class Util
{
    
    /**
     * DocCommentを取得する
     *
     * @param mixed $object
     * @return DocComment
     */
    static public function getDocComment($object)
    {
        return Reflection\ReflectionDocComment::create($object);
    }

    /**
     * コーラブル
     *
     * @param mixed $v
     * @return bool
     */
    static public function isCallable($v)
    {
        return is_callable($v) || (is_array($v) && is_callable($v[count($v)-1]));
    }

    /**
     * 完全なクラス名を取得する
     *
     * @param string $name
     * @param array $ns_list
     * @return 
     */
    static public function findClassName($name, $ns_list = [])
    {
        $class = false;

        if (class_exists($name))
        {
            $class = $name;
        }else{
            foreach($ns_list as $ns)
            {
                if (class_exists($ns.'\\'.$name))
                {
                    $class = $ns.'\\'.$name;
                }
            }
        }

        if ($class === false)
        {
            throw new Exception\ClassNotFound($name, $ns_list);
        }

        return $class;
    }

    /**
     * コンテントタイプを取得する
     */
    static public function getMimeType($filename)
    {
        static $finfo = false;
        if ($finfo === false)
        {
            $finfo = new finfo(FILEINFO_MIME); // mime タイプを mimetype 拡張形式で返します
        }
        return $finfo->buffer(file_get_contents($filename));
    }
    
}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
