<?php

namespace tiFy\Route;

use ArrayIterator;
use League\Route\RouteCollection as LeagueRouteCollection;
use tiFy\Route\RouteController;

class RouteCollectionController extends LeagueRouteCollection implements RouteCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * Récupération de l'itérateur.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }

    /**
     * {@inheritdoc}
     */
    public function has()
    {
        return !empty($this->routes);
    }

    /**
     * {@inheritdoc}
     */
    public function map($method, $path, $handler)
    {
        $path  = sprintf('/%s', ltrim($path, '/'));
        $route = (new RouteController())->setMethods((array) $method)->setPath($path)->setCallable($handler);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * Vérifie l'existance d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->routes);
    }

    /**
     * Récupération de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->routes[$key];
    }

    /**
     * Définition de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     * @param mixed $value Valeur à définir.
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) :
            $this->routes[] = $value;
        else :
            $this->routes[$key] = $value;
        endif;
    }

    /**
     * Suppression de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->routes[$key]);
    }
}