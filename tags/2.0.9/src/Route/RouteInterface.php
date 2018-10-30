<?php

namespace tiFy\Route;

use League\Route\ContainerAwareInterface;
use League\Route\Middleware\StackAwareInterface as MiddlewareAwareInterface;
use League\Route\Strategy\StrategyAwareInterface;

interface RouteInterface extends ContainerAwareInterface, MiddlewareAwareInterface, StrategyAwareInterface
{
    /**
     * Dispatch the route via the attached strategy.
     *
     * @param array $vars
     *
     * @return \League\Route\Middleware\ExecutionChain
     */
    public function getExecutionChain(array $vars);

    /**
     * Get the callable.
     *
     * @throws \RuntimeException
     *
     * @return callable
     */
    public function getCallable();

    /**
     * Set the callable.
     *
     * @param string|callable $callable
     *
     * @return \League\Route\Route
     */
    public function setCallable($callable);

    /**
     * Get the parent group.
     *
     * @return \League\Route\RouteGroup
     */
    //public function getParentGroup();

    /**
     * Set the parent group.
     *
     * @param \League\Route\RouteGroup $group
     *
     * @return \League\Route\Route
     */
    //public function setParentGroup(RouteGroup $group);

    /**
     * Get the path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Set the path.
     *
     * @param string $path
     *
     * @return \League\Route\Route
     */
    public function setPath($path);

    /**
     * Get the methods.
     *
     * @return string[]
     */
    public function getMethods();

    /**
     * Get the methods.
     *
     * @param string[] $methods
     *
     * @return \League\Route\Route
     */
    public function setMethods(array $methods);
}