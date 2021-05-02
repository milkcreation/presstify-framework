<?php

declare(strict_types=1);

namespace tiFy\Debug;

use Pollen\Debug\DebugBarInterface;
use Pollen\Debug\DebugManagerInterface;
use Pollen\Debug\ErrorHandlerInterface;
use Pollen\Debug\PhpDebugBarDriver;
use Pollen\Debug\WhoopsErrorHandler;
use tiFy\Container\ServiceProvider;

class DebugServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        DebugManagerInterface::class,
        DebugBarInterface::class,
        ErrorHandlerInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            DebugManagerInterface::class,
            function () {
                return new DebugManager(config('debug', []), $this->getContainer());
            }
        );

        $this->getContainer()->add(
            DebugBarInterface::class,
            function () {
                return new PhpDebugBarDriver($this->getContainer()->get(DebugManagerInterface::class));
            }
        );

        $this->getContainer()->share(
            ErrorHandlerInterface::class,
            function () {
                return new WhoopsErrorHandler($this->getContainer()->get(DebugManagerInterface::class));
            }
        );
    }
}