<?php
namespace Nora\Base\Secure;

use Nora\Base\Component\Componentable;
use Nora;

/**
 * View Facade
 *
 */
class Random
{
    private $_facade = null;

    public function __construct($facade)
    {
        $this->_facade = $facade;
    }

    /**
     * ランダムSha1を生成する
     *
     * @param int
     * @return string Sha1
     */
    public function sha1($size = 40)
    {
        return sha1($this->bytes($size));
    }

    /**
     * ランダムバイトを生成する
     *
     * @param int
     * @return string Sha1
     */
    public function bytes ($size = 40)
    {
        $strong = false;
        do {
            $bytes = openssl_random_pseudo_bytes($size, $strong);
        } while ($strong == false);
        return $bytes;
    }

    /**
     * ランダム文字列を生成する
     *
     * @param int
     * @param array
     * @return string
     */
    public function string($length = 16, $chars = ['-', '_', '.','$','#','%'])
    {
        $size = $max = $bytes = $ret = null;

        $allowed_chars = array_merge([
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
            'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
            'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y', 'Z',
            ], $chars);

        return $this->chars($length, $allowed_chars);
    }


    /**
     * ランダム数列を生成する
     *
     * @param int
     * @param array
     * @return string
     */
    public function number($length = 6)
    {
        $size = $max = $bytes = $ret = null;

        $allowed_chars = array_merge([
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        ]);

        return intval($this->chars($length, $allowed_chars));
    }

    /**
     * ランダム文字列を生成する
     *
     * @param int
     * @param array
     * @return string
     */
    public function chars($length, $allowed_chars)
    {
        $size = $max = $bytes = $ret = null;

        $max = count($allowed_chars) - 1;

        if (0x7FFFFFFF < $max) {
            return $this->chars($length, $chars);
        }

        $size = 4 * $length;
        $bytes = $this->bytes($size);
        $ret = '';
        for ($i = 0; $i < $length; $i++) {
            $var = unpack('Nint', substr($bytes, $i, 4))['int'] & 0x7FFFFFFF;
            $fp = (float) $var / 0x7FFFFFFF;
            $ret.= $allowed_chars[(int) round($max * $fp)];
        }
        return $ret;
    }

    /**
     * Sha256
     */
    public static function sha256($string)
    {
        return hash('sha256', $string);
    }

    /**
     * Saltを取り出す
     */
    public static function salts($string, $cost = 10)
    {
        $salts = [];

        // SALTを計算する
        $hash = '';
        for($i=0; $i<$cost; $i++)
        {
            $salts[] = $hash = self::sha256($hash.self::$salt.$string);
        }

        return $salts;
    }

    /**
     * Salt付きパスワードの生成
     */
    public static function securePasswordEncrypt($string, $cost=10, $stretched = 1000)
    {
        // SALTを複数作成する
        $salts = self::salts($string, $cost);

        // ランダムでひとつのSALTを使用する
        $salt = $salts[mt_rand(0, count($salts)-1)];

        // パスワードへSALTを足す
        $hash = '';
        for($i=0; $i<$stretched; $i++)
        {
            $hash = self::sha256($hash.$salt.$string);
        }
        return $hash;
    }

    /**
     * Salt付きパスワードの検証
     */
    public static function securePasswordVerify($string, $verify_hash, $cost = 10, $stretched = 1000)
    {
        // 発生したであろう全SALT値を検証
        foreach(self::salts($string, $cost) as $salt)
        {
            // ストレッチをかける
            $hash = '';
            for($i=0; $i<$stretched; $i++)
            {
                $hash = self::sha256($hash.$salt.$string);
            }


            if ($hash === $verify_hash) {
                return true;
            }
        }
        return false;
    }

    /**
     * Basic認証
     *
     * @return string user
     */
    public static function basic($cb, $message = 'AUTH')
    {
        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
        {
            if (self::securePasswordVerify($_SERVER['PHP_AUTH_PW'], $cb($_SERVER['PHP_AUTH_USER'])));
            {
                return $_SERVER['PHP_AUTH_USER'];
            }
        }

        header('WWW-Authenticate: Basic realm="'.$message.'"');
        header('Content-Type: text/plain; charset=utf-8');
        die('このページを見るには認証が必要です');

        // PASSWORDの生成方法
        //$password = $secure->securePasswordEncrypt('1234');
        //var_dump($password);
    }
}
