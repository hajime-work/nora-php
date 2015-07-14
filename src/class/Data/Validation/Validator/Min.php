<?php
namespace Nora\Data\Validation\Validator;

/**
 * バリデーション
 */
class min extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);
        $this->setParam('min', $args[0]);

        $this->message()->setMessage('must be longer then :min');
    }

    protected function validateImpl($value, $all)
    {
        return mb_strlen($value) >= intval($this->getParam('min'));
    }
}
