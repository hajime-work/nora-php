<?php
namespace Nora\Data\Validation\Validator;

/**
 * バリデーション
 */
class max extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);
        $this->setParam('max', $args[0]);

        $this->message()->setMessage('must be shorter then :max');
    }

    protected function validateImpl($value, $all)
    {
        return mb_strlen($value) <= intval($this->getParam('max'));
    }
}
