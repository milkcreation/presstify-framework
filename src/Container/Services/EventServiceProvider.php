<?php

declare(strict_types=1);

namespace tiFy\Container\Services;

use Pollen\Event\EventDispatcher;
use Pollen\Event\EventDispatcherInterface;
use tiFy\Container\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $provides = [
        EventDispatcherInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(EventDispatcherInterface::class, function () {
            return new EventDispatcher([], $this->getContainer());
        });
    }
}