<?php
/**
 * Nora Project
 *
 * @author Hajime MATSUMOTO <hajime@nora-worker.net>
 * @copyright 2015 nora-worker.net.
 * @licence https://www.nora-worker.net/LICENCE
 * @version 1.0.0
 */
namespace Nora\Base\Event;

use Nora\Base\Hash\Hash;

/**
 * イベント
 */
class Event extends Hash implements EventIF
{
    private $_tags;
    private $_stoped;
    private $_context;

    static public function create ($tags, $args, $context)
    {
        $ev = new Event();
        $ev->addTag($tags);
        $ev->initValues($args);
        $ev->_context = $context;
        return $ev;
    }

    public function __construct( )
    {
        $this->set_hash_option(Hash::OPT_IGNORE_CASE);
        $this->_tags = Hash::newHash([], Hash::OPT_IGNORE_CASE|Hash::OPT_ALLOW_UNDEFINED_KEY);
    }

    public function addTag($tag)
    {
        if (is_array($tag)) {
            foreach($tag as $v) $this->addTag($v);
            return $this;
        }

        $this->_tags[$tag] = $tag;
    }

    public function explain( )
    {
        $exp = [
            'tags' => $this->_tags->toArray(),
            'args' => $this->toArray()
        ];
        return $exp;
    }

    public function isStopPropagation( )
    {
        return $this->_stoped;
    }

    public function match($tags)
    {
        if (is_string($tags)) $tags = [$tags];

        foreach($tags as $t)
        {
            if ($this->_tags->inArray($t))
            {
                return true;
            }
        }
        return false;
    }

    public function getContext()
    {
        return $this->_context;
    }

    public function getTags( )
    {
        return $this->_tags;
    }

}
