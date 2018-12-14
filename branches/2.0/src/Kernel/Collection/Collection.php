<?php

namespace tiFy\Kernel\Collection;

use Illuminate\Support\Collection as IlluminateCollection;
use ArrayIterator;
use tiFy\Contracts\Kernel\Collection as CollectionContract;

class Collection implements CollectionContract
{
    /**
     * Liste des éléments.
     * @var array
     */
    protected $items = [];

    /**
     * {@inheritdoc}
     */
    public function collect($items = null)
    {
        return is_null($items) ? new IlluminateCollection($this->items) : new IlluminateCollection($items);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return !empty($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->items[$key] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function wrap($item)
    {
        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) :
            $this->items[] = $value;
        else :
            $this->items[$key] = $value;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    /**
     * {@inheritdoc}return app($name);
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
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
}