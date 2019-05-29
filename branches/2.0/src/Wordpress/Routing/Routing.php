<?php declare(strict_types=1);

namespace tiFy\Wordpress\Routing;

use Exception;
use FastRoute\Dispatcher as FastRoute;
use League\Route\Dispatcher;
use tiFy\Contracts\Routing\Router as RouterManager;
use tiFy\Http\Request;
use tiFy\Wordpress\Contracts\Routing as RoutingContract;
use tiFy\Wordpress\Routing\Strategy\Template as TemplateStrategy;

class Routing implements RoutingContract
{
    /**
     * Instance du gestionnaire de routage.
     * @var RouterManager
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param RouterManager $manager Instance du gestionnaire de routage.
     *
     * @return void
     */
    public function __construct(RouterManager $manager)
    {
        $this->manager = $manager;

        app()->get('wp.wp_query');

        app()->add('router.strategy.default', function () {
            return new TemplateStrategy();
        });

        add_action('parse_request', function () {
            try {
                $response = $this->manager->dispatch(Request::convertToPsr());

                if ($response->getStatusCode() !== 100) {
                    $this->manager->emit($response);
                    exit;
                }
                /*if ($response->getHeaders() || $response->getBody()->getSize()) {
                    exit;
                }*/
            } catch (Exception $e) {
                /**
                 * Suppression du slash de fin dans l'url des routes déclarées.
                 * @see https://symfony.com/doc/current/routing/redirect_trailing_slash.html
                 * @see https://stackoverflow.com/questions/30830462/how-to-deal-with-extra-in-phpleague-route
                 */
                if (config('routing.remove_trailing_slash', true)) {
                    $path = request()->getBaseUrl() . request()->getPathInfo();
                    $method = request()->getMethod();

                    if (($path != '/') && (substr($path, -1) == '/') && ($method === 'GET')) {
                        $match = (new Dispatcher($this->manager->getData()))->dispatch($method, rtrim($path, '/'));

                        if ($match[0] === FastRoute::FOUND) {
                            wp_redirect(request()->fullUrl());
                            exit;
                        }
                    }
                }
            }
        },0);
    }
}