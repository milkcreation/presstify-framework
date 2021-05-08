<?php

declare(strict_types=1);

namespace tiFy\Kernel;

use League\Uri\Http as HttpUri;
use Pollen\Http\Request;
use Pollen\Http\RequestInterface;
use tiFy\Container\ServiceProvider;

class KernelServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'uri',
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->getContainer()->share('path', function () { return new Path(); });

        $this->getContainer()->share('class-loader', new ClassLoader($this->getContainer()));

        $this->getContainer()->share('config', new Config($this->getContainer()));
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('uri', function () {
            /** @var Request $request */
            $request = $this->getContainer()->get(RequestInterface::class);

            return HttpUri::createFromString($request->getUri());
        });
    }
}