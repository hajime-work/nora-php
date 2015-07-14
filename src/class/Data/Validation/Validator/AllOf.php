<?php
namespace Nora\Data\Validation\Validator;

/**
 * バリデーショングループ
 */
class AllOf extends Group
{

    protected function validateImpl($val, $all)
    {
        $result = true;

        foreach($this->children() as $v)
        {
            if(!$v->validate($val))
            {
                $result = false;

                if ($all === false) return false;
            }
        }
        return $result;
    }
}
