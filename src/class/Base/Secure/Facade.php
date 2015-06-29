<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Secure;

use Nora\Base\Component\Componentable;
use Nora;

/**
 * View Facade
 *
 */
class Facade
{
    use Componentable;

    protected function initComponentImpl( )
    {
    }

    /**
     * Salt付きのパスワードMD5
     *
     * @param int
     * @return string
     */
    public static function SaltMd5 ($password, $size = 6)
    {
        $salt = self::randomString($size);
        $password = md5($password . $salt);
        return [
            $password,
            $salt
        ];
    }

    /**
     * Salt付きのパスワードMD5のVerify
     */
    public static function SaltMd5Verify ($in_password, $md5_password, $salt)
    {
        if (is_array($salt))
        {
            foreach($salt as $s)
            {
                if(self::SaltMd5Verify($in_password, $md5_password, $s))
                {
                    return true;
                }
            }
            return false;
        }

        $password = md5($in_password . $salt);
        return $password === $md5_password;
    }


    /**
     * ランダムSha1を生成する
     *
     * @param int
     * @return string Sha1
     */
    public static function randomSha1 ($size = 40)
    {
        return sha1(static::randomBytes($size));
    }

    /**
     * ランダムバイトを生成する
     *
     * @param int
     * @return string Sha1
     */
    public static function randomBytes ($size = 40)
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
    public static function randomString($length = 16, $chars = ['-', '_', '.','$','#','%'])
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

        return self::randomChars($length, $allowed_chars);
    }

    /**
     * ランダムパスワードを生成する
     *
     * @param int
     * @param array
     * @return string
     */
    public static function randomPassword($length = 8, $chars = ['-', '_', '.','$','#','%'])
    {
        if ($length < 4) throw new \Exception(
            'パスワードが短すぎます'
        );
        $password = self::randomString($length, $chars);

        // 強度のテスト　大文字,小文字,数字,記号の混在
        // 先頭,記号,数字不可
        if (
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/^[A-Za-z]/', $password)
        )
        {
            return $password;
        }
        return self::randomPassword($length, $chars=[]);
    }

    /**
     * ランダム数列を生成する
     *
     * @param int
     * @param array
     * @return string
     */
    public static function randomNumber($length = 6)
    {
        $size = $max = $bytes = $ret = null;

        $allowed_chars = array_merge([
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        ]);

        return self::randomChars($length, $allowed_chars);
    }

    public static function randomChars($length, $allowed_chars)
    {
        $size = $max = $bytes = $ret = null;

        $max = count($allowed_chars) - 1;

        if (0x7FFFFFFF < $max) {
            return self::randomChars($length, $chars);
        }

        $size = 4 * $length;
        $bytes = self::randomBytes($size);
        $ret = '';
        for ($i = 0; $i < $length; $i++) {
            $var = unpack('Nint', substr($bytes, $i, 4))['int'] & 0x7FFFFFFF;
            $fp = (float) $var / 0x7FFFFFFF;
            $ret.= $allowed_chars[(int) round($max * $fp)];
        }
        return $ret;
    }
}
