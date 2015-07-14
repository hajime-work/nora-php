<?php
namespace Nora\Data\Validation;

/**
 * バリデーションメッセージ
 */
class MessageGroup extends Message
{

    public function getError($offset = null)
    {
        if ($this->hasMessage())
        {
            return $this->buildMessage();
        }

        $messages = [];
        foreach($this->validator()->children() as $v)
        {
            if ($v->isInvalid())
            {
                if($v->hasParam('key'))
                {
                    $messages[$v->getParam('key')] = $v->message()->{__function__}();
                } else {
                    $messages[] = $v->message()->{__function__}();
                }
            }
        }

        if ($offset === null)
        {
            return $messages;
        }else{
            return $messages[intval($offset)];
        }
    }


}
