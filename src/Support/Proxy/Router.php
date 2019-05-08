<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Psr\Container\ContainerInterface;
use tiFy\Contracts\Routing\Route as RouteContract;
use tiFy\Contracts\Routing\RouteGroup as RouteGroupContract;

/**
 * @method static array all()
 * @method static int count()
 * @method static RouteContract|null current()
 * @method static string|null currentRouteName()
 * @method static RouteContract delete(string $path, callable $handler)
 * @method static bool exists()
 * @method static RouteContract get(string $path, callable $handler)
 * @method static ContainerInterface getContainer()
 * @method static RouteContract getNamedRoute(string $name)
 * @method static RouteGroupContract group(string $prefix, callable $group)
 * @method static RouteContract head(string $path, callable $handler)
 * @method static bool hasCurrent()
 * @method static bool hasNamedRoute(string $name)
 * @method static bool isCurrentNamed(string $name)
 * @method static RouteContract map(string $method, string $path, callable $handler)
 * @method static RouteContract patch(string $path, callable $handler)
 * @method static RouteContract post(string $path, callable $handler)
 * @method static RouteContract put(string $path, callable $handler)
 * @method static RouteContract options(string $path, callable $handler)
 * @method static string url(string $name, array $parameters = [], bool $absolute = true)
 * @method static RouteContract xhr(string $path, callable $handler, string $method)
 */
class Router extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'router';
    }
}