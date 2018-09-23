<?php

namespace tiFy\Kernel\Events;

use League\Event\Emitter;
use League\Event\Event;

class Events extends Emitter
{
    /**
     * {@inheritdoc}
     */
    public function listen($name, $listener, $priority = 0)
    {
        return $this->addListener($name, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function trigger($event)
    {
        if (!is_object($event) && !is_string($event)) :
            return null;
        endif;

        $args = func_get_args();
        $args[0] = is_object($event) ? $event : Event::named($event);

        return call_user_func_array([$this, 'emit'], $args);
    }
}