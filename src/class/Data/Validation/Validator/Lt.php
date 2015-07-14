<?php
namespace Nora\Data\Validation\Validator;

/**
 * バリデーション
 */
class lt extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);
        $this->setParam('lt', $args[0]);

        $this->message()->setMessage('must be less then :lt');
    }

    protected function validateImpl($value, $all)
    {
        return intval($value) < intval($this->getParam('lt'));
    }
}
