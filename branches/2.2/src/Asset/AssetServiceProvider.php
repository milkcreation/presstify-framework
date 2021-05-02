<?php

declare(strict_types=1);

namespace tiFy\Asset;

use Pollen\Asset\AssetManagerInterface;
use tiFy\Container\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
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