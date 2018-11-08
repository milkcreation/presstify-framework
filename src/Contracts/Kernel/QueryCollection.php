<?php

namespace tiFy\Contracts\Kernel;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface QueryCollection extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Instanciation du controleur de traitement d'une collection d'élément.
     *
     * @param null|array $items Liste des éléments à traiter. Si null utilise la liste des éléments déclarés.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collect($items = null);

    /**
     * Récupération de la liste des éléments.
     *
     * @return array
     */
    public function all();

    /**
     * Compte le nombre d'éléments.
     *
     * @return int
     */
    public function count();

    /**
     * Récupération d'une intance de l'itérateur.
     *
     * @return callable
     */
    public function getIterator();

    /**
     * Vérification d'existance d'un élément depuis l'itération.
     *
     * @param mixed $key Clé d'indexe.
     *
     * @return boolean
     */
    public function offsetExists($key);

    /**
     * Récupération d'un élément depuis l'itération.
     *
     * @param mixed $key Clé d'indexe.
     *
     * @return boolean
     */
    public function offsetGet($key);

    /**
     * Définition d'un élément depuis l'itération.
     *
     * @param mixed $key Clé d'indexe.
     * @param mixed $value Valeur.
     *
     * @return boolean
     */
    public function offsetSet($key, $value);

    /**
     * Suppression d'un élément depuis l'itération.
     *
     * @param mixed $key Clé d'indexe.
     *
     * @return boolean
     */
    public function offsetUnset($key);

    /**
     * Récupération d'un élément depuis l'itération.
     *
     * @param mixed $key Clé d'indexe.
     *
     * @return mixed
     */
    public function __get($key);

    /**
     * Définition d'un élément depuis l'itération.
     *
     * @param mixed $key Clé d'indexe.
     * @param mixed $value Valeur.
     *
     * @return void
     */
    public function __set($key, $value);

    /**
     * Vérification d'existance d'un élément depuis l'itération.
     *
     * @param mixed $key Clé d'indexe.
     *
     * @return boolean
     */
    public function __isset($key);

    /**
     * Suppression d'un élément depuis l'itération.
     *
     * @param mixed $key Clé d'indexe.
     *
     * @return void
     */
    public function __unset($key);
}