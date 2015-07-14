<?php
namespace Nora\Data\Validation\Validator;

use Nora\Data\Validation\MessageRelated;
/**
 * バリデーション-依存
 */
class Related extends Validator
{
    private $_related;

    public function validate($value, $all = false)
    {
        $res = $this->validateImpl($value, $all);
        if ($res === false) $this->invalid();
        return $res;
    }

    protected function setRelated($v)
    {
        $this->_related = $v;
    }

    public function getRelated()
    {
        return $this->_related;
    }


    public function setup($args)
    {
        parent::setup($args);
        $this->_message = new MessageRelated($this);
    }
 
}
