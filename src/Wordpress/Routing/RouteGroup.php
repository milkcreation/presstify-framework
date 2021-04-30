<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Routing;

use League\Route\Middleware\MiddlewareAwareInterface;
use tiFy\Routing\Concerns\MiddlewareAwareTrait;
use tiFy\Wordpress\Contracts\Routing\RouteGroup as RouteGroupContract;
use tiFy\Wordpress\Routing\Concerns\WpQueryAwareTrait;
use tiFy\Routing\RouteGroup as BaseRouteGroup;

class RouteGroup extends BaseRouteGroup implements RouteGroupContract
{
    use WpQueryAwareTrait;

    /**
     * Add a middleware as a class name to the stack
     *
     * @param string $middleware
     *
     * @return MiddlewareAwareTrait|MiddlewareAwareInterface
     */
    public function lazyMiddleware(string $middleware): MiddlewareAwareInterface
    {
        $this->middleware[] = $this->getContainer()->get($middleware);

        return $this;
    }

    /**
     * Add multiple middlewares as class names to the stack
     *
     * @param string[] $middlewares
     *
     * @return MiddlewareAwareTrait|MiddlewareAwareInterface
     */
    public function lazyMiddlewares(array $middlewares): MiddlewareAwareInterface
    {
        foreach ($middlewares as $middleware) {
            $this->lazyMiddleware($middleware);
        }

        return $this;
    }
}