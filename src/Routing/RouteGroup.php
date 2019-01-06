<?php declare(strict_types=1);

namespace tiFy\Routing;

use League\Route\RouteGroup as LeagueRouteGroup;
use League\Route\Route as LeagueRoute;
use League\Route\RouteCollectionInterface;
use tiFy\Contracts\Routing\RouteGroup as RouteGroupContract;
use Psr\Container\ContainerInterface;
use tiFy\Contracts\Routing\Router;

class RouteGroup extends LeagueRouteGroup implements RouteGroupContract
{
    use RouteRegisterMapTrait;

    /**
     * Instance du contrôleur de routage.
     * @var Router
     */
    protected $collection;

    /**
     * CONSTRUCTEUR.
     *
     * @param string prefix
     * @param callable $callback
     * @param RouteCollectionInterface $collection
     *
     * @return void
     */
    public function __construct(string $prefix, callable $callback, RouteCollectionInterface $collection)
    {
        parent::__construct($prefix, $callback, $collection);

        call_user_func($this->callback, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke() : void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getContainer() : ContainerInterface
    {
        return $this->collection->getContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function map(string $method, string $path, $handler) : LeagueRoute
    {
        $path  = ($path === '/')
            ? $this->prefix
            : ($this->prefix === '/' ? '' : $this->prefix). sprintf('/%s', ltrim($path, '/'));

        $route = $this->collection->map($method, $path, $handler);

        $route->setParentGroup($this);

        if ($host = $this->getHost()) :
            $route->setHost($host);
        endif;

        if ($scheme = $this->getScheme()) :
            $route->setScheme($scheme);
        endif;

        if ($port = $this->getPort()) :
            $route->setPort($port);
        endif;

        if (is_null($route->getStrategy()) && ! is_null($this->getStrategy())) :
            $route->setStrategy($this->getStrategy());
        endif;

        return $route;
    }
}