<?php
namespace Nora\Data\Validation\Validator;

/**
 * バリデーショングループ
 */
class OneOf extends Group
{
    private $_children = [];


    protected function validateImpl($val)
    {
        foreach($this->_children as $v)
        {
            if($v->validate($val))
            {
                return true;
            }
        }
        return false;
    }
}
