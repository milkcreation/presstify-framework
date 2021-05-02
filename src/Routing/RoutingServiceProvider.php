<?php

declare(strict_types=1);

namespace tiFy\Routing;

use Laminas\Diactoros\ResponseFactory;
use Pollen\Routing\Middleware\XhrMiddleware;
use Pollen\Routing\RouterInterface;
use Pollen\Routing\Strategy\ApplicationStrategy;
use Pollen\Routing\Strategy\JsonStrategy;
use tiFy\Container\ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{
    protected $provides = [
        RouterInterface::class,
        'routing.middleware.xhr',
        'routing.strategy.app',
        'routing.strategy.json',
        'url',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(RouterInterface::class, function () {
            return new Router(config('router', []), $this->getContainer());
        });

        $this->getContainer()->share('url', function () {
            return new Url($this->getContainer()->get(RouterInterface::class));
        });

        $this->registerMiddlewares();
        $this->registerStrategies();

    }

    /**
     * Déclaration des middlewares.
     *
     * @return void
     */
    public function registerMiddlewares(): void
    {
        $this->getContainer()->add('routing.middleware.xhr', function () {
            return new XhrMiddleware();
        });
    }

    /**
     * Déclaration des stratégies.
     *
     * @return void
     */
    public function registerStrategies(): void
    {
        $this->getContainer()->add('routing.strategy.app', function () {
            $applicationStrategy = new ApplicationStrategy();
            $applicationStrategy->setContainer($this->getContainer());

            return $applicationStrategy;
        });
        $this->getContainer()->add('routing.strategy.json', function () {
            $jsonStrategy = new JsonStrategy(new ResponseFactory());
            $jsonStrategy->setContainer($this->getContainer());

            return $jsonStrategy;
        });
    }
}