<?php
namespace Nora\Data\Sanitize\Sanitizer;

/**
 * サニタイザー
 */
class Offset extends Group
{
    private $_v;

    public function setup($vars)
    {
        parent::setup($vars);

        $key = $vars[0];
        if (is_array($key))
        {
            foreach($key as $k=>$v) {
                $this->_list[$k] = $v;
            }
        }else{
            $this->_list[$key] = $vars[1];
        }
    }

    public function sanitizeImpl($val)
    {
        $ret = [];
        foreach($this->_list as $k=>$v)
        {
            $ret[$k] = $v->sanitize($val[$k]);
        }
        return $ret;
    }
}
