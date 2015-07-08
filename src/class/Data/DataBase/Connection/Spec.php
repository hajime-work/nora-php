<?php
namespace Nora\Data\DataBase\Connection;

use Nora\Util;


/**
 * コネクションを表現するスペックライン
 *
 */
class Spec extends Util\Spec
{
    /**
     * scheme://host/field?key=value
     *
     * <ex>
     * 'fspot' => 'mysqli://fspot.mysql.slave/fspot?user=fspot&pass=deganjue';
     * 'fspot-mongo' => 'mongo://mongodb.fspot/fspot?replicaSet=fspot',
     * 'fspot-redis' => 'redis://redis01.fuzoku.gallery'
     * </ex>
     */
    public function parse($string)
    {
        if (!preg_match('/
            (?<scheme>.+):\/\/
            (?<host>.[^\/:?]+)
            (?::(?<port>[0-9]+)){0,1}
            (?:\/(?<field>[^\?]+)){0,1}
            (?:\?(?<attrs>.+)){0,1}/x', $string, $m))
        {
            throw new Exception\IlegalSpecFormat($string);
        }

        $this->set('scheme', $m['scheme']);
        $this->set('host', $m['host']);

        if (isset($m['port']) && !empty($m['port']))
        {
            $this->set('port', intval($m['port']));
        }

        if (isset($m['field']) && !empty($m['field']))
        {
            $this->set('field', $m['field']);
        }

        if (isset($m['attrs']) && !empty($m['attrs']))
        {
            parse_str($m['attrs'], $q);
            $this->setAttr($q);
        }
    }
}
