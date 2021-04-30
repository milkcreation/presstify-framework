<?php

declare(strict_types=1);

namespace tiFy\Contracts\Routing;

use League\Route\RouteCollectionInterface;
use League\Route\RouteConditionHandlerInterface;
use League\Route\Middleware\MiddlewareAwareInterface;
use League\Route\Strategy\StrategyAwareInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @mixin \tiFy\Routing\Concerns\MiddlewareAwareTrait
 */
interface RouteGroup extends
    ContainerAwareTrait,
    MiddlewareAwareInterface,
    RouteCollectionAwareTrait,
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
     * Définition d'un ou plusieurs middlewares associés.
     *
     * @param string|string[]|MiddlewareInterface|MiddlewareInterface[] $middleware
     *
     * @return static
     */
    public function middleware($middleware): MiddlewareAwareInterface;
}