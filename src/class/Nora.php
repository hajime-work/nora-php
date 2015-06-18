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
use Nora\Base\Hash;

class Nora
{
    /**
     * デバッグ用 var_dumpのラッパー
     *
     * @param mixed $val
     * @return void
     */
    static public function debug($val)
    {
        if (is_object($val) && method_exists($val, '__debugInfo'))
        {
            var_dump($val->__debugInfo());
        }else{
            var_Dump($val);
        }
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
    static public function hash($defaults = [], $options = Hash::OPT_SECURE)
    {
        return Hash::newHash($defaults, $options);
    }
}
