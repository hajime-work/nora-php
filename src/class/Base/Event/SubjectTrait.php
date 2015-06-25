<?php
/**
 * Nora Project
 * ============
 *
 * @since 20150618
 * @author kurari@hajime.work
 */
Namespace Nora\Base\Event;

use Nora\Base\Hash\ObjectHash;

/**
 * イベント: サブジェクト
 */
trait SubjectTrait
{
    private $_observers;

    /**
     * Fire
     */
    public function fire ($tag, $args = [])
    {
        if ($tag instanceof Event) {
            $event = $tag;
        }else{
            $event = Event::create($tag, $args, $this);
        }

        if ($event->isStopPropagation()) return $event;

        foreach($this->observers() as $o)
        {
            if(false === $o->notify($event))
            {
                $event->stopPropagation();
                break;
            }
        }

        return $event;
    }

    /**
     * オブザーバを保存する
     */
    public function observers( )
    {
        if (!isset($this->_observers))
        {
            $this->_observers = new ObjectHash();
        }
        return $this->_observers;
    }

    /**
     * オブザーバを追加する
     */
    public function attach($spec)
    {
        if (!($spec instanceof ObserverIF))
        {
            $spec = Observer::create($spec);
        }
        $this->observers()->add($spec);
        return $this;
    }

}

/* vim: set foldmethod=marker st=4 ts=4 sw=4 et: */
