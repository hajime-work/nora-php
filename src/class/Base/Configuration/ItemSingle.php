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

/**
 * 設定管理アイテム[Single]
 */
class ItemSingle extends Item
{
    private $_value;

    public function setValue($data)
    {
        if (is_array($data))
        {
            throw new Exception\CantSetArrayToSingle();
        }

        $this->_value = $data;
    }

    public function toArray()
    {
        return [$this->_value];
    }

    public function read()
    {
        return $this->_value;
    }
}

