<?php

namespace tiFy\Routing;

use League\Route\RouteGroup as LeagueRouteGroup;
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
     * {@inheritdoc}
     */
    public function getContainer() : ContainerInterface
    {
        return $this->collection->getContainer();
    }
}