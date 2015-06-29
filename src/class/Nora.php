<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */

use Nora\Base\Hash;
use Nora\Util\Util;
use Nora\Base\NoraEngine;

class Nora
{
    private static $_engine = null;

    static public function __callStatic($name, $params)
    {
        if (self::$_engine === null)
            self::$_engine = new NoraEngine();

        return call_user_func_array([self::$_engine,$name], $params);
    }


    /**
     * デバッグ用 var_dumpのラッパー
     *
     * @param mixed $val
     * @return void
     */
    static public function debug($val, $buf = false)
    {
        if ($buf == true) 
        {
            ob_start();
        }

        if (is_object($val) && method_exists($val, '__debugInfo'))
        {
            var_dump($val->__debugInfo());
        }else{
            var_Dump($val);
        }

        if ($buf == true)
        {
            $result = ob_get_contents();
            ob_end_clean();
        }
        return $result;
    }
    static public function debugParams($val)
    {
        if (empty($val)) {
            return sprintf(
                '%s (%s)', 'empty', gettype($val));
        }
        ob_start();
        var_dump($val, true);
        $str = ob_get_contents();
        ob_end_clean();
        return $str;
    }


    /**
     * 多言語対応用のラッパー
     *
     * @param string $key フォーマット
     * @param array フォーマットに投入する配列
     * @return string
     */
    static public function message($key, $params = [])
    {
        return vsprintf($key, $params);
    }

    /**
     * ハッシュ作成用
     *
     * @param array $defaults
     * @param int $options
     */
    static public function hash($defaults = [], $options = Hash\Hash::OPT_SECURE)
    {
        return Hash\Hash::newHash($defaults, $options);
    }


}
