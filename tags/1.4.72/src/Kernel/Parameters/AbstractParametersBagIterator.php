<?php

namespace tiFy\Kernel\Parameters;

use tiFy\Contracts\Kernel\ParametersBagIteratorInterface;
use \ArrayIterator;

abstract class AbstractParametersBagIterator extends AbstractParametersBag implements ParametersBagIteratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function __call($method, $parameters)
    {
        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}