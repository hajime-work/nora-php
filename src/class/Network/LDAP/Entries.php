<?php
namespace Nora\Network\LDAP;

use Nora\Base\Event;
use Nora\Base\Component\Component;
use Nora\Network\API\Twitter;
use Nora;

/**
 * LDAP: Connection
 */
class Entries implements \IteratorAggregate
{
    public function __construct($con, $result)
    {
        $this->_con = $con;
        $this->_result = $result;
    }

    public function getIterator( )
    {
        foreach(ldap_get_entries($this->_con, $this->_result) as $e)
        {
            yield $e;
        }
    }

    public function each($cb)
    {
        foreach($this as $k=>$v)
        {
            $cb($v, $k);
        }
    }

    public function map($cb)
    {
        $res = [];
        foreach($this as $k=>$v)
        {
            $res = $cb($v, $k);
        }
        return $res;
    }

}
