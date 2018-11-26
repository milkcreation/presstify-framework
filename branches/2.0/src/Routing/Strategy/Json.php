<?php

namespace tiFy\Routing\Strategy;

use League\Route\Strategy\JsonStrategy;

class Json extends JsonStrategy
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