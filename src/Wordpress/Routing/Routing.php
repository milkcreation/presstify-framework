<?php declare(strict_types=1);

namespace tiFy\Wordpress\Routing;

use Exception;
use FastRoute\Dispatcher as FastRoute;
use League\Route\Dispatcher;
use tiFy\Contracts\Routing\{
    Route as BaseRouteContract,
    RouteGroup as BaseRouteGroupContract,
    Router as BaseRouterContract
};
use tiFy\Http\{Request, RedirectResponse};
use tiFy\Support\Proxy\Request as req;
use tiFy\Wordpress\Contracts\Routing\{
    Route as RouteContract,
    Routing as RoutingContract
};
use tiFy\Wordpress\Routing\Controller\WordpressController;
use tiFy\Wordpress\Routing\Strategy\Template as TemplateStrategy;

class Routing implements RoutingContract
{
    /**
     * Instance du gestionnaire de routage.
     * @var BaseRouterContract
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param BaseRouterContract $manager Instance du gestionnaire de routage.
     *
     * @return void
     */
    public function __construct(BaseRouterContract $manager)
    {
        $this->manager = $manager;

        if (is_multisite()) {
            $this->manager->setPrefix(get_blog_details()->path);
        }

        $this->manager->getContainer()->get('wp.wp_query');

        $this->registerOverride();

        add_action('parse_request', function () {
            /** @var RouteContract $default */
            $default = $this->manager->get('/{path:.*}', [new WordpressController(), 'index'])
                ->setName('wordpress');
            $default->setWpQuery(true);

            try {
                $response = $this->manager->dispatch(Request::convertToPsr());

                if ($response->getStatusCode() !== 100) {
                    $this->manager->emit($response);
                    exit;
                }
            } catch (Exception $e) {
                /**
                 * Suppression du slash de fin dans l'url des routes déclarées.
                 * @see https://symfony.com/doc/current/routing/redirect_trailing_slash.html
                 * @see https://stackoverflow.com/questions/30830462/how-to-deal-with-extra-in-phpleague-route
                 */
                if (config('routing.remove_trailing_slash', true)) {
                    $permalinks = get_option('permalink_structure');
                    if (substr($permalinks, -1) == '/') {
                        update_option('permalink_structure',  rtrim($permalinks, '/'));
                    }

                    $path = req::getBaseUrl() . req::getPathInfo();
                    $method = req::getMethod();

                    if (($path != '/') && (substr($path, -1) == '/') && ($method === 'GET')) {
                        $dispatcher = new Dispatcher($this->manager->getData());
                        $match = $dispatcher->dispatch($method, rtrim($path, '/'));

                        if ($match[0] === FastRoute::FOUND) {
                            $redirect_url = rtrim($path, '/');
                            $redirect_url .= ($qs = req::getQueryString()) ? "?{$qs}" : '';

                            $response = RedirectResponse::createPsr($redirect_url);
                            $this->manager->emit($response);
                            exit;
                        }
                    }
                }
            }
        }, 0);
    }

    /**
     * Déclaration des controleurs de surchage.
     *
     * @return void
     */
    public function registerOverride(): void
    {
        $this->manager->getContainer()->add('router.strategy.default', function () {
            return new TemplateStrategy();
        });

        $this->manager->getContainer()->add(
            BaseRouteContract::class,
            function (string $method, string $path, callable $handler, $collection) {
                return new Route($method, $path, $handler, $collection);
            }
        );

        $this->manager->getContainer()->add(
            BaseRouteGroupContract::class,
            function (string $prefix, callable $handler, $collection) {
                return new RouteGroup($prefix, $handler, $collection);
            }
        );
    }
}