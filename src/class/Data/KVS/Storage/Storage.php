<?php
namespace Nora\Data\KVS\Storage;

use Nora\Data\KVS;
use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * Key Value Storage
 */
abstract class Storage extends Component\Component implements KVS\StorageIF
{
    protected function initComponentImpl( )
    {
    }

    abstract public function initStorage($con, $spec);
}
