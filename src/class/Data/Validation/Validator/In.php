<?php
namespace Nora\Data\Validation\Validator;

/**
 * バリデーション
 */
class In extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);
        $this->setParam('in', implode(',', $args[0]));
        $this->setParam('array', $args[0]);

        $this->message()->setMessage('must be leth then :in');
    }

    protected function validateImpl($value, $all)
    {
        return in_array($value, $this->getParam('array'));
    }
}
