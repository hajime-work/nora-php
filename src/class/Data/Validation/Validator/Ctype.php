<?php
namespace Nora\Data\Validation\Validator;

/**
 *
 * ctype_alnum
 * ctype_alpha
 * ctype_cntrl
 * ctype_digit
 * ctype_graph
 * ctype_lower
 * ctype_print
 * ctype_punct
 * ctype_space
 * ctype_upper
 * ctype_xdigit
 */
class ctype extends Validator
{
    protected function setup($args)
    {
        parent::setup($args);
        $this->setParam('type', $args[0]);

        $this->message()->setMessage('must be :type');
    }

    protected function validateImpl($value, $all)
    {
        $func = 'ctype_'.$this->getParam('type');
        if ( $func((string)$value) )
        {
            return $func;
        }
    }
}
