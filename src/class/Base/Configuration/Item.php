<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Configuration;

use Nora\Base\Hash;

/**
 * 設定管理アイテム
 */
abstract class Item
{
    const SEP = '.';

    private $_items = [];
    private $_value = [];

    public function __construct($data = null)
    {
        $this->setValue($data);
    }

    abstract public function setValue($data);

    public function dump( )
    {
        var_Dump($this->toArray());
    }

}

