<?php
namespace Nora\Data\DataBase\Client\Dir;

use Nora\Data\DataBase\Client\Base;
use Nora\Base\Hash;
use Nora;
use Nora\Util\Util;

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
        $dir = $spec->host.'/'.$spec->field;

        if (!file_exists($dir) && !mkdir($dir, 0777, true))
        {
            throw new DirException(Nora::message('%sは作成できません', $dir.'/'.$field));
        }

        if (!is_dir($dir))
        {
            throw new DirException(Nora::message('%sはディレクトリではありません', $dir));
        }

        if (!is_writable($dir))
        {
            throw new DirException(Nora::message('%sは書き込みできません', $dir));
        }


        $this->setConnection($dir);
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

    public function ensure($key)
    {
        $file = $this->keyToFile($key);
        $this->ensureFile($file);
    }

    public function swipe($time)
    {
        foreach(Util::getFileList($this->con()) as $file)
        {
            Nora::logDebug(sprintf("%s %s %s<br>", $file,date('Y/m/d G:i:s', fileatime($file)), $old = time() - fileatime($file)));

            if ($old < $time)
            {
                unlink($file);
            }
        }
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
