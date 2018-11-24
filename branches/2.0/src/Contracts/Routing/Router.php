<?php

namespace tiFy\Contracts\Routing;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use League\Route\Dispatcher;
use League\Route\Middleware\StackAwareInterface as MiddlewareAwareInterface;
use League\Route\Strategy\StrategyAwareInterface;
use League\Route\RouteCollectionInterface;
use Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\ServerRequestInterface;

interface Router extends
    ArrayAccess,
    Countable,
    IteratorAggregate,
    MiddlewareAwareInterface,
    RouteCollectionInterface,
    StrategyAwareInterface
{
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
     * Récupération de la liste des routes déclarés.
     *
     * @return
     */
    public function all();

    /**
     * Compte de le nombre de routes déclarés.
     *
     * @return int
     */
    public function count();

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
     * Dispatch the route based on the request.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response);

    /**
     * Emission de la réponse.
     *
     * @param ResponseInterface $response Réponse HTTP.
     *
     * @return string
     */
    public function emit(ResponseInterface $response);

    /**
     * Vérification d'existance de routes déclarées.
     *
     * @param string Nom de qualification de la route.
     *
     * @return boolean
     */
    public function exists();

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
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData();

    /**
     * Return a fully configured dispatcher.
     *
     * @param ServerRequestInterface $request
     *
     * @return Dispatcher
     */
    public function getDispatcher(ServerRequestInterface $request);

    /**
     * Get named route.
     *
     * @param string $name
     *
     * @return \League\Route\Route
     */
    public function getNamedRoute($name);

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
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     */
    public function head($route, $handler);

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
     * Déclaration d'une route.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param array $attrs Attributs de configuration.
     *
     * @return null|array
     */
    public function register($name, $attrs = []);

    /**
     * Définition de la route courante.
     *
     * @param string $name Nom de qualification.
     * @param array $args Liste des variables passés en arguments.
     *
     * @return void
     */
    public function setCurrent($name, $args = []);
}