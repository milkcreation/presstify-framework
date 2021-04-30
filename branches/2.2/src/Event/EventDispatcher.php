<?php

declare(strict_types=1);

namespace tiFy\Event;

use Pollen\Event\EventDispatcher as BaseEventDispatcher;

class EventDispatcher extends BaseEventDispatcher
{
    public function listen(string $name, $listener, int $priority = 0): void
    {
        $this->on($name, $listener, $priority);
    }
}
