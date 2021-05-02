<?php

declare(strict_types=1);

namespace tiFy\Kernel;

use League\Uri\Http as HttpUri;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Http\Request;
use Pollen\Http\RequestInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequestInterface;
use tiFy\Container\ServiceProvider;
use tiFy\Event\EventDispatcher;
use tiFy\Support\ClassInfo;

class KernelServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'class-info',
        EventDispatcherInterface::class,
        PsrRequestInterface::class,
        RequestInterface::class,
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
        $this->getContainer()->add('class-info', function ($class) {
            return new ClassInfo($class);
        });

        $this->getContainer()->share(EventDispatcherInterface::class, function () {
            return new EventDispatcher();
        });

        $this->getContainer()->share(RequestInterface::class, function () {
            return Request::getFromGlobals();
        });

        $this->getContainer()->share(PsrRequestInterface::class, function () {
            return Request::createPsr();
        });

        $this->getContainer()->share('uri', function () {
            /** @var Request $request */
            $request = $this->getContainer()->get(RequestInterface::class);

            return HttpUri::createFromString($request->getUri());
        });
    }
}