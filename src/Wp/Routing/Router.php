<?php

namespace tiFy\Wp\Routing;

use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Routing\RouteHandler as RouteHandlerContract;
use tiFy\Contracts\Routing\Router as RouterContract;

class Router
{
    public function __construct()
    {
        app()->add(RouteHandlerContract::class, function ($name, $attrs, $router) {
            return new RouteHandler($name, $attrs, $router);
        });

        add_action(
            'wp',
            function () {
                /**
                 * Suppression du slash de fin dans l'url
                 * @see https://symfony.com/doc/current/routing/redirect_trailing_slash.html
                 * @see https://stackoverflow.com/questions/30830462/how-to-deal-with-extra-in-phpleague-route
                 */
                if (config('route.remove_trailing_slash', false)) :
                    $path = request()->getBaseUrl() . request()->getPathInfo();

                    if(
                        ($path != '/') &&
                        (substr($path, -1) == '/') &&
                        (request()->getMethod() === 'GET') &&
                        ((new Collection(router()->all()))->first(
                            function($route) use ($path) {
                                /** @var RouteInterface $route */
                                return (in_array('GET', $route->getMethods()) && preg_match('#^'. preg_quote($route->getPath(), '/') . '/$#', $path));
                            })
                        )
                    ) :
                        wp_safe_redirect(request()->fullUrl(), 301);
                        exit;
                    endif;
                endif;

                try {
                    $response = router()->dispatch(app()->get(ServerRequestInterface::class));

                    if ($response->getBody()->getSize()) :
                        router()->emit($response);
                        exit;
                    endif;
                } catch (\Exception $e) {

                }
            },
            0
        );
    }
}