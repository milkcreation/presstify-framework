<?php

namespace tiFy\Wp\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    public function __construct()
    {
        add_action(
            'wp_loaded',
            function () {
                /** @var RouterContract $router */
                $router = app()->get('router');

                $router->register(
                    'toto',
                    [
                        'method' => 'GET',
                        'path' => '/toto',
                        'cb' => function () {
                            return view()->setDirectory(__DIR__)->make('test');
                        }
                    ]
                );

                try {
                    $response = $router->dispatch(
                        app()->get(ServerRequestInterface::class),
                        app()->get(ResponseInterface::class)
                    );
                    $router->emit($response);
                    exit;
                } catch (\Exception $e) {

                }
            },
            0
        );
    }
}