<?php
namespace Nora\Data\Sanitize\Sanitizer;

/**
 * サニタイザー
 */
class Group extends Sanitizer
{
    private $_children;

    public function createSanitizer($type, $args)
    {
        $class = __namespace__.'\\'.ucfirst($type);
        $this->_children[] = $class::create($args);
        return $this;
    }
        

    public function __call($name, $args)
    {
        return $this->createSanitizer($name, $args);
    }

    public function children( )
    {
        return $this->_children;
    }

    public function sanitizeImpl($val)
    {
        foreach($this->children() as $s)
        {
            $val = $s->sanitize($val);
        }
        return $val;
    }
}
