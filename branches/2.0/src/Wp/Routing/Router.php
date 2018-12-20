<?php

namespace tiFy\Wp\Routing;

use FastRoute\Dispatcher as FastRoute;
use League\Route\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        app()->add('router.strategy.default', function () {
            return new TemplateStrategy();
        });

        add_action(
            'wp',
            function () {
                try {
                    $response = router()->dispatch(app()->get(ServerRequestInterface::class));

                    router()->emit($response);

                    if ($response->getHeaders() || $response->getBody()->getSize()) :
                        exit;
                    endif;
                } catch (\Exception $e) {
                    /**
                     * Suppression du slash de fin dans l'url des routes déclarées.
                     * @see https://symfony.com/doc/current/routing/redirect_trailing_slash.html
                     * @see https://stackoverflow.com/questions/30830462/how-to-deal-with-extra-in-phpleague-route
                     */
                    if (config('routing.remove_trailing_slash', true)) :
                        $path = request()->getBaseUrl() . request()->getPathInfo();
                        $method = request()->getMethod();

                        if (($path != '/') && (substr($path, -1) == '/') && ($method === 'GET')) :
                            $match = (new Dispatcher(router()->getData()))->dispatch($method, rtrim($path, '/'));

                            if ($match[0] === FastRoute::FOUND) :
                                wp_redirect(request()->fullUrl());
                                exit;
                            endif;
                        endif;
                    endif;
                }
            },
            0
        );
    }
}