<?php
namespace Nora\Data\Validation;

use Nora\Exception;

/**
 * バリデーションメッセージ
 */
class Message extends Exception
{
    private $_validator;
    private $_message = false;

    public function __construct(Validator\Validator $validator)
    {
        $this->_validator = $validator;
    }

    public function setMessage($val)
    {
        $this->_message = $val;
    }

    public function getMessageRaw( )
    {
        return $this->_message;
    }

    public function buildMessage($map = [])
    {
        return preg_replace_callback('/:([a-zA-Z]+)/', function($m) use ($map){

            if (array_key_exists($m[1], $map))
            {
                return $map[$m[1]];
            }



            if ($this->_validator->hasParam($m[1]))
            {
                return $this->_validator->getParam($m[1]);
            }
            return $m[0];

        },$this->_message);
    }

    public function hasMessage( )
    {
        return !empty($this->_message);
    }

    public function validator()
    {
        return $this->_validator;
    }

    public function getError()
    {
        return $this->buildMessage();
    }

    public function __toString( )
    {
        return "ValidationError: ".implode("\\", $this->getError());
    }

}
