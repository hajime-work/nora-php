<?php
namespace Nora\Base\Secure;

use Nora\Base\Component\Componentable;
use Nora;

/**
 * View Facade
 *
 */
class Password
{
    public $salt = 'NORA';
    public $saltCount = 10;
    public $stretch = 1000;

    private $_stretch = null;
    private $_facade = null;

    protected function __construct($facade)
    {
        $this->_facade = $facade;
    }

    protected function setup($array)
    {
        foreach($array as $k=>$v) $this->$k = $v;
    }

    public function create($facade, $array)
    {
        $obj = new Password($facade);
        $obj->setup($array);
        return $obj;
    }

    /**
     * Salt付きパスワードの生成
     */
    public function hash($string)
    {
        // SALTを複数作成する
        $salts = $this->salts($string);

        // ランダムでひとつのSALTを使用する
        $salt = $salts[mt_rand(0, count($salts)-1)];

        // パスワードへSALTを足す
        return $this->stretch($salt, $string);
    }


    /**
     * Salt付きパスワードの検証
     */
    public function verify($string, $verify_hash)
    {
        // 発生したであろう全SALT値を検証
        foreach($this->salts($string) as $salt)
        {
            if ($this->stretch($salt, $string) === $verify_hash) {
                return true;
            }
        }
        return false;
    }

    /**
     * ランダムパスワードを生成する
     *
     * @param int
     * @param array
     * @return string
     */
    public function generate($length = 8, $chars = ['-', '_', '.','$','#','%'])
    {
        if ($length < 4) throw new \Exception(
            'パスワードが短すぎます'
        );
        $password = $this->_facade->random()->string($length, $chars);

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
        return $this->generate($length, $chars=[]);
    }

    /**
     * Sha256
     */
    private function sha256($string)
    {
        return hash('sha256', $string);
    }

    /**
     * Saltを取り出す
     */
    private function salts($string)
    {
        $salts = [];

        // SALTを計算する
        $hash = '';
        for($i=0; $i<$this->saltCount; $i++)
        {
            $salts[] = $hash = $this->sha256($hash.$this->salt.$string);
        }

        return $salts;
    }

    private function stretch($salt, $string)
    {
        $hash = '';
        for($i=0; $i<$this->stretch; $i++)
        {
            $hash = $this->sha256($hash.$salt.$string);
        }
        return $hash;
    }
}
