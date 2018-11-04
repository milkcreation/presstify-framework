<?php

namespace tiFy\Contracts\Form;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface FactoryItemsIterator extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Récupération d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return mixed
     */
    public function __get($key);

    /**
     * Définition d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     * @param mixed $value Valeur.
     *
     * @return void
     */
    public function __set($key, $value);

    /**
     * Vérification d'existance d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return boolean
     */
    public function __isset($key);

    /**
     * Suppression d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return void
     */
    public function __unset($key);

    /**
     * Compte le nombre d'éléments.
     *
     * @return int
     */
    public function count();

    /**
     * Récupération d'un élément.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Récupération d'une intance de l'itérateur.
     *
     * @return callable
     */
    public function getIterator();

    /**
     * Vérification d'existance d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return boolean
     */
    public function offsetExists($key);

    /**
     * Récupération d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return boolean
     */
    public function offsetGet($key);

    /**
     * Définition d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     * @param mixed $value Valeur.
     *
     * @return boolean
     */
    public function offsetSet($key, $value);

    /**
     * Suppression d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return boolean
     */
    public function offsetUnset($key);
}