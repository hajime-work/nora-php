<?php
namespace Nora\Data\Validation\Validator;

/**
 * バリデーション
 */
class gt extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);
        $this->setParam('gt', $args[0]);

        $this->message()->setMessage('must be grater then :gt');
    }

    protected function validateImpl($value, $all)
    {
        return intval($value) > intval($this->getParam('gt'));
    }
}
