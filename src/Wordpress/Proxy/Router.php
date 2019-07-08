<?php declare(strict_types=1);

namespace tiFy\Wordpress\Proxy;

use tiFy\Support\Proxy\Router as BaseRouter;
use tiFy\Wordpress\Contracts\Routing\Route;

/**
 * @method static Route|null current()
 * @method static Route delete(string $path, callable $handler)
 * @method static Route get(string $path, callable $handler)
 * @method static Route getNamedRoute(string $name)
 * @method static Route head(string $path, callable $handler)
 * @method static Route map(string $method, string $path, callable $handler)
 * @method static Route patch(string $path, callable $handler)
 * @method static Route post(string $path, callable $handler)
 * @method static Route put(string $path, callable $handler)
 * @method static Route options(string $path, callable $handler)
 * @method static Route xhr(string $path, callable $handler, string $method = 'POST')
 */
class Router extends BaseRouter
{

}