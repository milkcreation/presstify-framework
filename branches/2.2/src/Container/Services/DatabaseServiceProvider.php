<?php

declare(strict_types=1);

namespace tiFy\Container\Services;

use Pollen\Database\DatabaseManager;
use Pollen\Database\DatabaseManagerInterface;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class DatabaseServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        DatabaseManagerInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(DatabaseManagerInterface::class, function () {
           return new DatabaseManager();
        });
    }
}