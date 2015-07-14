<?php
namespace Nora\Data\Validation\Validator;

use Nora\Data\Validation\Message;
use Nora;

/**
 * バリデーション
 */
class Validator 
{
    protected $_message;
    private $_params;
    private $_message_params = [];
    private $_last_invalid = false;

    public static function create($args)
    {
        $class = get_called_class();
        $v = new $class();
        call_user_func([$v,'setup'], $args);
        return $v;
    }

    protected function setParam($k, $v)
    {
        $this->_params->setVal($k, $v);
    }

    public function getParam($k)
    {
        return $this->_params->getVal($k);
    }
    
    public function hasParam($k)
    {
        return $this->_params->hasVal($k);
    }

    protected function setup($args)
    {
        $this->_message = new Message($this);
        $this->_params = Nora::Hash();
    }

    public function message( )
    {
        return $this->_message;
    }

    public function setMessage($val)
    {
        $this->message()->setMessage($val);
        return $this;
    }


    public function validate($value, $all = false)
    {
        if (is_null($value)) return true;


        $res = $this->validateImpl($value, $all);
        if ($res === false) $this->invalid();
        return $res;
    }

    public function assert($value)
    {
        if ($this->validate($value, true))
        {
            return null;
        }

        throw $this->message();
    }

    protected function invalid( )
    {
        return $this->_last_invalid = true;
    }

    public function isInvalid( )
    {
        return $this->_last_invalid;
    }

}
