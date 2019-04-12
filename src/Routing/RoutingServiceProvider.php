<?php declare(strict_types=1);

namespace tiFy\Routing;

use Http\Factory\Diactoros\ResponseFactory;
use tiFy\Container\ServiceProvider;
use tiFy\Routing\Middleware\Xhr;
use tiFy\Routing\Strategy\App;
use tiFy\Routing\Strategy\Json;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

class RoutingServiceProvider extends ServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        'router',
        'router.emitter',
        'router.middleware.xhr',
        'router.strategy.default',
        'router.strategy.json'
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerEmitter();
        $this->registerMiddleware();
        $this->registerRouter();
        $this->registerStrategies();
        $this->registerUrl();
    }

    /**
     * Déclaration du contrôleur d'émission de la réponse HTTP.
     *
     * @return void
     */
    public function registerEmitter()
    {
        $this->getContainer()->share('router.emitter', function () {
            return new SapiEmitter();
        });
    }

    /**
     * Déclaration des Middlewares.
     *
     * @return void
     */
    public function registerMiddleware()
    {
        $this->getContainer()->add('router.middleware.xhr', function () {
            return new Xhr();
        });
    }

    /**
     * Déclaration du controleur de routage.
     *
     * @return void
     */
    public function registerRouter()
    {
        $this->getContainer()->share('router', function () {
            return new Router($this->getContainer());
        });
    }

    /**
     * Déclaration des controleurs de strategies.
     *
     * @return void
     */
    public function registerStrategies()
    {
        $this->getContainer()->add('router.strategy.default', new App());

        $this->getContainer()->add('router.strategy.json', new Json(new ResponseFactory()));
    }

    /**
     * Déclaration du controleur d'urls.
     *
     * @return void
     */
    public function registerUrl()
    {
        $this->getContainer()->share('url', function () {
            return new Url($this->getContainer()->get('router'), request());
        });

        $this->getContainer()->add('url.factory', UrlFactory::class);
    }
}