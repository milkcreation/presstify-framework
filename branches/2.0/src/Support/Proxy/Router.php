<?php declare(strict_types=1);

namespace tiFy\Support\Proxy;

use tiFy\Contracts\Routing\Route as RouteContract;

/**
 * @method static array all()
 * @method static RouteContract getNamedRoute(string $name)
 */
class Router extends AbstractProxy
{
    public static function getInstanceIdentifier()
    {
        return 'router';
    }
}