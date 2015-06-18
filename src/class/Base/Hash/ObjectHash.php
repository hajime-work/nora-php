<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Hash;

/**
 * ハッシュクラス
 */
class ObjectHash extends Hash
{
    private $_object_lists = [];

    public function add($object)
    {
        $this->_setVal($key = spl_object_hash($object), []);
        $this->_object_lists[$key] = $object;
    }

    public function &getVal($object, $valaue = null)
    {
        return parent::getVal(spl_object_hash($object), $value);
    }

    public function setVal($object, $valaue = null)
    {
        return parent::setVal(spl_object_hash($object), $value);
    }

    public function hasVal($object)
    {
        return parent::hasVal(spl_object_hash($object));
    }

    public function delVal($object)
    {
        return parent::delVal(spl_object_delh($object));
    }

    public function getIterator( )
    {
        foreach($this->getKeys() as $k)
        {
            yield $k => $this->_object_lists[$k];
        }
    }
}
