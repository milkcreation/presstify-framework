<?php

namespace tiFy\Routing;

use Http\Factory\Diactoros\ResponseFactory;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use tiFy\App\Container\AppServiceProvider;
use tiFy\Routing\Strategy\App;
use tiFy\Routing\Strategy\Json;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

class RoutingServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services fournis.
     * @var array
     */
    protected $provides = [
        'router',
        'router.emitter',
        'router.strategy.default',
        'router.strategy.json',
        ServerRequestInterface::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action('after_setup_tify', function () {
            $this->getContainer()->get('router');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerRouter();
        $this->registerUrl();
        $this->registerPsrRequest();
        $this->registerEmitter();
        $this->registerStrategies();
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
     * Déclaration des contrôleurs de réponse et de requête HTTP.
     *
     * @return void
     */
    public function registerPsrRequest()
    {
        $this->getContainer()->share(ServerRequestInterface::class, function () {
            return (new DiactorosFactory())->createRequest(request());
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
            return new Router($this->app);
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
    }
}