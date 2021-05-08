<?php

declare(strict_types=1);

namespace tiFy\Container\Services;

use Pollen\Filesystem\StorageManager;
use Pollen\Filesystem\StorageManagerInterface;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class FilesystemServiceProvider extends BaseServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        StorageManagerInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(StorageManagerInterface::class, function () {
            return new StorageManager([], $this->getContainer());
        });
    }
}