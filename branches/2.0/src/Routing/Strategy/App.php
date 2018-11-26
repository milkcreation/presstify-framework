<?php

namespace tiFy\Routing\Strategy;

use League\Route\Strategy\ApplicationStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Routing\Route as RouteContract;

class App extends ApplicationStrategy
{
    /**
     * {@inheritdoc}
     */
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request) : ResponseInterface
    {
        /** @var RouteContract $route */
        $route->setCurrent();

        return parent::invokeRouteCallable($route, $request);
    }
}