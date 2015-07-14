<?php
namespace Nora\Data\Validation\Validator;

use Nora\Data\Validation\MessageGroup;

/**
 * バリデーショングループ
 */
class Group extends Validator
{
    private $_children;

    public function createValidator($type, $args)
    {
        $class = __namespace__.'\\'.ucfirst($type);
        $this->_children[] = $class::create($args);
        return $this;
    }

    public function setup($args)
    {
        parent::setup($args);
        $this->_message = new MessageGroup($this);
    }
        

    public function __call($name, $args)
    {
        return $this->createValidator($name, $args);
    }

    public function children( )
    {
        return $this->_children;
    }

    public function validate($value, $all = false)
    {
        $res = $this->validateImpl($value, $all);
        if ($res === false) $this->invalid();
        return $res;
    }


    protected function validateImpl($val, $all)
    {
        $result = true;

        foreach($this->_children as $v)
        {
            if(!$v->validate($val, $all))
            {
                $result = false;
                if ($all !== true) return false;
            }
        }
        return $result;
    }

}
