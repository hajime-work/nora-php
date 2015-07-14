<?php
namespace Nora\Data\Validation\Validator;

/**
 * バリデーション
 */
class Number extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);
        $this->message()->setMessage('only number');
    }

    protected function validateImpl($value)
    {
        return is_numeric($value);
    }
}
