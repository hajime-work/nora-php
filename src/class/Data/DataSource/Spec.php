<?php
namespace Nora\Data\DataSource;

use Nora\Util;


/**
 * コネクションを表現するスペックライン
 *
 */
class Spec extends Util\Spec
{
    /**
     */
    public function parse($string)
    {
        if (!preg_match('/
            (?<database>.+):\/\/
            (?:(?<field>[^?]+)){0,1}
            (?:\?(?<attrs>.+)){0,1}/x', $string, $m))
        {
            throw new Exception\IlegalSpecFormat($string);
        }

        foreach($m as $k=>$v)
        {
            if (is_numeric($k)) continue;
            $this->set($k, $v);
        }
    }
}
