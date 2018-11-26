<?php

namespace tiFy\Contracts\Routing;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use League\Route\Dispatcher;
use League\Route\Middleware\MiddlewareAwareInterface;
use League\Route\Strategy\StrategyInterface;
use League\Route\Strategy\StrategyAwareInterface;
use League\Route\Route;
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
     * Dispatch the route based on the request.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request);

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
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData();

    /**
     * Récupération d'une route déclarée selon son nom de qualification.
     *
     * @param string $name Nom de qualification.
     *
     * @return Route
     */
    public function getNamedRoute(string $name) : Route;

    /**
     * Add a group of routes to the collection.
     *
     * @param string   $prefix
     * @param callable $group
     *
     * @return \League\Route\RouteGroup
     */
    public function group(string $prefix, callable $group);

    /**
     * Vérification d'existance d'une route associé à la requête HTTP courante.
     *
     * @return boolean
     */
    public function hasCurrent();

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
     * @param array $attrs {
     *  Attributs de configuration.
     *
     *  @var string $method Méthode de traitement de la requête. GET|POST|PUT|PATCH|DELETE|HEAD|OPTIONS.
     *  @var string $path Chemin relatif.
     *  @var callable $cb
     *  @var string $scheme Condition de traitement du schema de l'url. http|https.
     *  @var string $host Condition de traitement relative au domaine. ex. example.com.
     *  @var string|StrategyInterface $strategy Controleur de traitement de la route répondant à la requête HTTP courante. html|json|StrategyInterface.
     *  @todo string $group
     * }
     *
     * @return void
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