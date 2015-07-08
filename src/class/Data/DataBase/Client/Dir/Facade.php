<?php
namespace Nora\Data\DataBase\Client\Dir;

use Nora\Data\DataBase\Client\Base;
use Nora\Base\Hash;
use Nora;

use MongoClient;

/**
 * ディレクトリDB用のFacade
 *
 */
class Facade extends Base\Facade
{
    private $_dir;

    protected function initClient($spec)
    {
        $dir = $spec->host;

        if (!is_dir($dir))
        {
            throw new DirException(Nora::message('%sはディレクトリではありません', $dir));
        }

        if (!is_writable($dir))
        {
            throw new DirException(Nora::message('%sは書き込みできません', $dir));
        }

        $field = $spec->field;

        if (!file_exists($dir.'/'.$field) && !mkdir($dir.'/'.$field))
        {
            throw new DirException(Nora::message('%sは作成できません', $dir.'/'.$field));
        }

        $this->setConnection($dir.'/'.$field);
    }

    public function get($key)
    {
        $file = $this->keyToFile($key);
        return $this->unserialize(file_get_contents($file));
    }

    public function set($key, $value)
    {
        $file = $this->keyToFile($key);
        $this->ensureFile($file);
        return file_put_contents($file, $this->serialize($value));
    }

    public function has($key)
    {
        $file = $this->keyToFile($key);
        if (file_exists($file))
        {
            return true;
        }
        return false;
    }

    public function delete($key)
    {
        $file = $this->keyToFile($key);
        unlink($file);
    }

    private function serialize($value)
    {
        return serialize($value);
    }

    private function unserialize($value)
    {
        return unserialize($value);
    }

    private function keyToFile($key)
    {
        $string = base64_encode($key);
        $file = $this->con().'/'.implode("/", str_split($string, 3));
        return $file;
    }

    private function ensureFile($file)
    {
        $dir = dirname($file);
        if (!file_exists($dir))
        {
            mkdir($dir, 0777, true);
        }
        touch($file);
        return true;
    }
}
