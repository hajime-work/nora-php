<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Hash;

use ArrayAccess;
use IteratorAggregate;
use Countable;

interface HashIF extends ArrayAccess,IteratorAggregate,Countable
{
    public function setVal($name, $value);
    public function &getVal($name, $value = null);
    public function hasVal($name);
    public function delVal($name);
}
