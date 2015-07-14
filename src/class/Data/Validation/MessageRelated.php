<?php
namespace Nora\Data\Validation;

/**
 * バリデーションメッセージ
 */
class MessageRelated extends Message
{

    public function getError($offset = null)
    {
        $msg = implode(',', $this->validator()->getRelated()->message()->getError());


        return $this->buildMessage(['msg' => $msg]);
    }


}
