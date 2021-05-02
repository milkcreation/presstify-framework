<?php

declare(strict_types=1);

namespace tiFy\Container\Services;

use Pollen\Log\LogManager;
use Pollen\Log\LogManagerInterface;
use tiFy\Container\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        LogManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(LogManagerInterface::class, function () {
            return new LogManager(config('log', []), $this->getContainer());
        });
    }
}