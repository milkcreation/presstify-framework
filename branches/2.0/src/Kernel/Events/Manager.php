<?php

namespace tiFy\Kernel\Events;

use League\Event\Emitter;
use tiFy\Contracts\Kernel\EventsManager;

class Manager extends Emitter implements EventsManager
{
    /**
     * @inheritDoc
     */
    public function listen($name, $listener, $priority = 0)
    {
        $listener = new Listener($listener);

        return $this->addListener($name, $listener, $priority);
    }

    /**
     * @inheritDoc
     */
    public function on($name, $listener, $priority = 0)
    {
        return $this->listen($name, $listener, $priority);
    }

    /**
     * @inheritDoc
     */
    public function trigger($event, $args = [])
    {
        if(func_num_args() === 1) {
            $_args = func_get_args();
            array_push($_args, []);
        }

        return call_user_func_array([$this, 'emit'], $_args ?? func_get_args());
    }
}