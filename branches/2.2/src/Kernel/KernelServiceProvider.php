<?php

declare(strict_types=1);

namespace tiFy\Kernel;

use League\Uri\Http as HttpUri;
use tiFy\Http\Request;
use tiFy\Http\Response;
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
        'events',
        'request',
        'response',
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

        $this->getContainer()->share('events', function () {
            return new EventDispatcher();
        });

        $this->getContainer()->share('request', function () {
            return Request::setFromGlobals();
        });

        $this->getContainer()->share('response', function () {
            return new Response();
        });

        $this->getContainer()->share('uri', function () {
            /** @var Request $request */
            $request = $this->getContainer()->get('request');

            return HttpUri::createFromString($request->getUri());
        });
    }
}