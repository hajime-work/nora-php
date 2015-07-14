<?php
namespace Nora\Data\Validation\Validator;

/**
 * オフセット
 */
class Offset extends Related
{
    private $_v;

    public function setup($vars)
    {
        parent::setup($vars);

        $this->setParam('key', $vars[0]);
        $this->setRelated($vars[1]);
        $this->setMessage(':key :msg');
    }

    protected function validateImpl($value, $all)
    {
        if(!is_array($value)) return true;

        $key = $this->getParam('key');
        if(!array_key_exists($key, $value)) {
            $value[$key] = null;
        }
        return $this->getRelated()->validate($value[$key], $all);
    }
}
