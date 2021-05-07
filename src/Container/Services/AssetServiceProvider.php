<?php

declare(strict_types=1);

namespace tiFy\Container\Services;

use Pollen\Asset\AssetManager;
use Pollen\Asset\AssetManagerInterface;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class AssetServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        AssetManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(AssetManagerInterface::class, function () {
            return new AssetManager(config('asset', []), $this->getContainer());
        });
    }
}