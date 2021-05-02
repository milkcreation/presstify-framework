<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Pollen\Http\RedirectResponseInterface;
use Pollen\Http\RequestInterface;
use Pollen\Routing\RouteInterface;
use Pollen\Routing\RouteGroupInterface;
use Pollen\Routing\RouterInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @method static RouterInterface addRoute(RouteInterface $route)
 * @method static RouteInterface|null current()
 * @method static string|null currentRouteName()
 * @method static RouteInterface delete(string $path, callable $handler)
 * @method static RouteInterface get(string $path, callable $handler)
 * @method static string getBasePrefix()
 * @method static RequestInterface getHandleRequest()
 * @method static RouteInterface getNamedRoute(string $name)
 * @method static RedirectResponseInterface getNamedRouteRedirect(string $name, array $args = [], bool $isAbsolute = false, int $status = 302, array $headers = [])
 * @method static string|null getNamedRouteUrl(string $name, array $args = [], bool $isAbsolute = false)
 * @method static RedirectResponseInterface getRouteRedirect(RouteInterface $route, array $args = [], bool $isAbsolute = false, int $status = 302, array $headers = [])
 * @@method static string|null getRouteUrl(RouteInterface $route, array $args = [], bool $isAbsolute = false)
 * @method static RouteGroupInterface group(string $prefix, callable $group)
 * @method static RouteInterface head(string $path, callable $handler)
 * @method static RouteInterface map(string $method, string $path, callable $handler)
 * @method static RouteInterface middleware(MiddlewareInterface $middleware)
 * @method static RouteInterface patch(string $path, callable $handler)
 * @method static RouteInterface post(string $path, callable $handler)
 * @method static RouteInterface put(string $path, callable $handler)
 * @method static RouteInterface options(string $path, callable $handler)
 * @method static RouterInterface setBasePrefix(?string $prefix)
 * @method static RouterInterface setCurrentRoute(RouterInterface $route)
 * @method static RouterInterface setFallback(callable|string $fallaback)
 * @method static RouterInterface setHandleRequest(RequestInterface $handleRequest)
 */
class Router extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return RouterInterface
     */
    public static function getInstance(): RouterInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return RouterInterface::class;
    }
}