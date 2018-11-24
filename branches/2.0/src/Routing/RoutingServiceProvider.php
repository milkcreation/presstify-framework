<?php

namespace tiFy\Routing;

use App\Views\ViewController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use tiFy\Contracts\Routing\RouteHandler as RouteHandlerContract;
use tiFy\Contracts\Routing\Router as RouterContract;
use tiFy\App\Container\AppServiceProvider;
use Zend\Diactoros\Response as PsrResponse;
use Zend\Diactoros\Response\SapiEmitter;

class RoutingServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        'route',
        'router',
        'router.emitter',
        RouteHandlerContract::class,
        ResponseInterface::class,
        ServerRequestInterface::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerRouter();
        $this->registerPsrHttp();
        $this->registerEmitter();
        $this->registerRoute();
    }

    /**
     * Déclaration du contrôleur d'émission de la réponse HTTP.
     *
     * @return void
     */
    public function registerEmitter()
    {
        $this->app->share('router.emitter', function () {
            return new SapiEmitter();
        });
    }

    /**
     * Déclaration des contrôleurs de réponse et de requête HTTP.
     *
     * @return void
     */
    public function registerPsrHttp()
    {
        $this->app->share(ResponseInterface::class, function () {
            return new PsrResponse();
        });

        $this->app->share(ServerRequestInterface::class, function () {
            return (new DiactorosFactory())->createRequest(request());
        });
    }

    /**
     * Déclaration des controleurs de route.
     *
     * @return void
     */
    public function registerRoute()
    {
        $this->app->add('route', function () {
            return new Route();
        });

        $this->app->add(RouteHandlerContract::class, function ($name, $attrs = [], RouterContract $router) {
            return new RouteHandler($name, $attrs, $router);
        });
    }

    /**
     * Déclaration du controleur de routage.
     *
     * @return void
     */
    public function registerRouter()
    {
        $this->app->share('router', function () {
            return new Router($this->app);
        });
    }
}