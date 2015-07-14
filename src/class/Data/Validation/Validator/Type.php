<?php
namespace Nora\Data\Validation\Validator;

/**
 *
 */
class type extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);
        $this->setParam('type', $args[0]);

        $this->message()->setMessage('must be :type');
    }

    protected function validateImpl($value, $all)
    {
        $res =  strtolower(gettype($value)) === strtolower($this->getParam('type'));
        return $res;
    }
}
