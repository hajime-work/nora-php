<?php
namespace Nora\Data\Cache;

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
            (?<database>.+):\/\/
            (?:(?<field>[^?]+)){0,1}
            (?:\?(?<attrs>.+)){0,1}/x', $string, $m))
        {
            var_dump($string);
            die();
            throw new Exception\IlegalSpecFormat($string);
        }

        foreach($m as $k=>$v)
        {
            if (is_numeric($k)) continue;
            $this->set($k, $v);
        }
    }
}
