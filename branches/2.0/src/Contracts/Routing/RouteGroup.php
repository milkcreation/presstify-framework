<?php declare(strict_types=1);

namespace tiFy\Contracts\Routing;

use League\Route\RouteCollectionInterface;
use League\Route\RouteConditionHandlerInterface;
use League\Route\Middleware\MiddlewareAwareInterface;
use League\Route\Strategy\StrategyAwareInterface;

interface RouteGroup extends
    ContainerAwareTrait,
    MiddlewareAwareInterface,
    RegisterMapAwareTrait,
    RouteCollectionInterface,
    RouteConditionHandlerInterface,
    StrategyAwareInterface,
    StrategyAwareTrait
{
    /**
     * Récupération du préfixe du groupe
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Définition du nom de qualification de la route.
     *
     * @return Route
     */
    public function name(string $name): RouteGroup;
}