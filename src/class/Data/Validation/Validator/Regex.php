<?php
namespace Nora\Data\Validation\Validator;

/**
 *
 */
class Regex extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);
        $this->setParam('regex', $args[0]);

        $this->message()->setMessage('must be mutch :regex');
    }

    protected function validateImpl($value, $all)
    {
        return preg_match($this->getParam('regex'), $value);
    }
}
