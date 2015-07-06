<?php
namespace Nora\Data\Cache\Adapter;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * キャッシュアダプター
 */
interface AdapterIF
{
    public function set($name, $value, $options = []);

    public function get($name);

    public function delete($name);

    public function has($name, $expire = -1, $create_after = -1);
}
