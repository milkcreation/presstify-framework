<?php

namespace tiFy\Kernel\Item;

use Illuminate\Support\Fluent;
use ArrayIterator;

abstract class AbstractItemIterator extends AbstractItemController implements ItemIteratorInterface
{
    /**
     * Handle dynamic calls to the container to set attributes.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->attributes);
    }

    /**
     * Récupération de l'itérateur.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Vérifie l'existance d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Récupération de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Définition de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     * @param mixed $value Valeur à définir.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Suppression de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Convert the Fluent instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Convert the Fluent instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}