<?php
namespace Nora\Data\Validation\Validator;

use Nora;

/**
 * バリデーション
 */
class NotNull extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);

        $this->message()->setMessage('must not be null');
    }

    public function validate($value, $all = false)
    {
        if (is_null($value))
        {
            $this->invalid();
            return false;
        }

        if (ctype_space($value))
        {
            $this->invalid();
            return false;
        }
        return true;
    }
}
