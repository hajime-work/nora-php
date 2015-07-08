<?php
namespace Nora\Data\KVS;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * ストレージIF
 */
interface StorageIF
{
    public function get($key);

    public function set($key, $value);

    public function has($data);

    public function delete($key);

    public function swipe($time);

    public function ensure($key);
}
