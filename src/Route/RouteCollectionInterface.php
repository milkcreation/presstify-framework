<?php

namespace tiFy\Route;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use League\Route\Middleware\StackAwareInterface as MiddlewareAwareInterface;
use League\Route\RouteCollectionInterface as LeagueRouteCollectionInterface;
use League\Route\Strategy\StrategyAwareInterface;
use tiFy\Route\RouteInterface;
use Zend\Diactoros\Response as ServerResponse;
use Zend\Diactoros\ServerRequest;

interface RouteCollectionInterface extends
    ArrayAccess,
    Countable,
    IteratorAggregate,
    MiddlewareAwareInterface,
    LeagueRouteCollectionInterface,
    StrategyAwareInterface
{
    /**
     * Récupération de la liste des routes déclarées.
     *
     * @return RouteInterface[]
     */
    public function all();

    /**
     * Retourne le nombre d'éléments trouvés.
     *
     * @return int
     */
    public function count();

    /**
     * Vérification d'existance d'éléments
     *
     * @return bool
     */
    public function has();

    /**
     * {@inheritdoc}
     */
    public function map($method, $path, $handler);

    /**
     * Add a group of routes to the collection.
     *
     * @param string   $prefix
     * @param callable $group
     *
     * @return \League\Route\RouteGroup
     */
    public function group($prefix, callable $group);

    /**
     * Dispatch the route based on the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    // public function dispatch(ServerRequestInterface $request, ResponseInterface $response);

    /**
     * Return a fully configured dispatcher.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \League\Route\Dispatcher
     */
    //public function getDispatcher(ServerRequestInterface $request);

    /**
     * Get named route.
     *
     * @param string $name
     *
     * @return \League\Route\Route
     */
    public function getNamedRoute($name);

    /**
     * Add a convenient pattern matcher to the internal array for use with all routes.
     *
     * @param string $alias
     * @param string $regex
     *
     * @return void
     */
    public function addPatternMatcher($alias, $regex);

    /**
     * Adds a route to the collection.
     *
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethod
     * @param string $route
     * @param mixed  $handler
     */
    public function addRoute($httpMethod, $route, $handler);

    /**
     * Create a route group with a common prefix.
     *
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param callable $callback
     */
    public function addGroup($prefix, callable $callback);

    /**
     * Adds a GET route to the collection
     *
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function get($route, $handler);

    /**
     * Adds a POST route to the collection
     *
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function post($route, $handler);

    /**
     * Adds a PUT route to the collection
     *
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function put($route, $handler);

    /**
     * Adds a DELETE route to the collection
     *
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function delete($route, $handler);

    /**
     * Adds a PATCH route to the collection
     *
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function patch($route, $handler);

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function head($route, $handler);

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData();
}