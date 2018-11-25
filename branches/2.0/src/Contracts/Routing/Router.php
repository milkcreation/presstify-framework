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
     * Récupération de la route courante associée à la requête HTTP.
     *
     * @return null|Route
     */
    public function current();

    /**
     * Récupération du nom de qualification de la route courante associée à la requête HTTP.
     *
     * @return string
     */
    public function currentRouteName();

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
     * Récupération d'une route déclarée selon son nom de qualification.
     *
     * @param string $name Nom de qualification.
     *
     * @return Route
     */
    public function getNamedRoute($name);

    /**
     * Récupération des motifs de traitement des arguments des routes déclarées.
     *
     * @return array
     */
    public function getPatternMatchers();

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
     * Vérification d'existance d'une route associé à la requête HTTP courante.
     *
     * @return boolean
     */
    public function hasCurrent();

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
     * Vérification de correspondance avec le nom de qualification de la route associé à la requête HTTP courante.
     *
     * @param string $name Nom de qualification à contrôler.
     * 
     * @return boolean
     */
    public function isCurrentNamed($name);

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
     * Redirection vers une route déclarée.
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $parameters Liste des variables passées en argument.
     * @param int $status Code de redirection.
     * @see https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
     *
     * @return void
     */
    public function redirect($name, $parameters = [], $status = 302);
    
    /**
     * Définition de la route courante.
     *
     * @param string $name Nom de qualification.
     * @param array $args Liste des variables passés en arguments.
     *
     * @return void
     */
    public function setCurrent($name, $args = []);

    /**
     * Récupération de l'url d'une route.
     *
     * @param  string $name Nom de qualification.
     * @param  array $parameters Liste des variables passées en argument.
     * @param  boolean $absolute Activation de l'url absolue.
     *
     * @return string
     */
    public function url($name, $parameters = [], $absolute = true);
}