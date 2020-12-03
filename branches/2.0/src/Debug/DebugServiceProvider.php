<?php declare(strict_types=1);

namespace tiFy\Debug;

use tiFy\Contracts\Debug\Debug as DebugManagerContract;
use tiFy\Contracts\Debug\DebugDriver as DebugDriverContract;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class DebugServiceProvider extends BaseServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        DebugManagerContract::class,
        DebugDriverContract::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(DebugManagerContract::class, function () {
            return new Debug(config('debug', []), $this->getContainer());
        });

        $this->getContainer()->share(DebugDriverContract::class, function () {
            return new PhpDebugBarDriver($this->getContainer()->get(DebugManagerContract::class));
        });
    }
}