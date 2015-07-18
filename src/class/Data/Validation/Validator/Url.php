<?php
namespace Nora\Data\Validation\Validator;

use Nora;

/**
 *
 */
class Url extends Regex
{
    private $_check_exists = false;

    protected function setup($args)
    {
        parent::setup([
            '/^http[s]{0,1}:\/\//'
        ]);


        $this->_check_exists = isset($args[0]) && $args[0] === true;

        $this->message()->setMessage('must be url');
    }

    protected function validateImpl($value, $all)
    {
        if (!parent::validateImpl($value))
        {
            return false;
        }

        if ($this->_check_exists)
        {
            Nora::logDebug('url---'.$value);
            if(false ===  Nora::HTTP()->checkUrl($value, $st))
            {
                $this->message()->setMessage('http status '.$st);
                return false;
            }
        }

        return true;
    }
}
