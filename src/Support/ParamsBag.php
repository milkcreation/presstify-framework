<?php

namespace tiFy\Support;

use ArrayIterator;
use tiFy\Contracts\Support\ParamsBag as ParamsBagContract;

class ParamsBag implements ParamsBagContract
{
    /**
     * Liste des paramètres.
     * @var array
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param null|array $attrs Liste des paramètres personnalisés.
     *
     * @return void
     */
    public function __construct($attrs = null)
    {
        if (!is_null($attrs)) :
            $this->parse($attrs);
        endif;
    }

    /**
     * @inheritdoc
     */
    public function __call($method, $parameters)
    {
        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * Récupération d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Définition d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     * @param mixed $value Valeur.
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Vérification d'existance d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Suppression d'un élément d'itération.
     *
     * @param string|int $key Clé d'indexe.
     *
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->attributes);
    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * @inheritdoc
     */
    public function json($options = 0)
    {
        return json_encode($this->all(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->all();
    }

    /**
     * @inheritdoc
     */
    public function keys()
    {
        return array_keys($this->attributes);
    }

    /**
     * @inheritdoc
     */
    public function parse($attrs = [])
    {
        $this->attributes = array_merge(
            $this->attributes,
            $this->defaults(),
            $attrs
        );
    }

    /**
     * @inheritdoc
     */
    public function pull($key, $default = null)
    {
        return Arr::pull($this->attributes, $key, $default);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * @inheritdoc
     */
    public function push($key, $value)
    {
        if (!$this->has($key)) :
            $this->set($key, []);
        endif;

        $arr = $this->get($key);

        if (!is_array($arr)) :
            return false;
        else :
            array_push($arr, $value);
            $this->set($key, $arr);

            return true;
        endif;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $k => $v) :
            Arr::set($this->attributes, $k, $v);
        endforeach;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function unshift($value, $key)
    {
        if (!$this->has($key)) :
            $this->set($key, []);
        endif;

        $arr = $this->get($key);

        if (!is_array($arr)) :
            return false;
        else :
            array_unshift($arr, $value);
            $this->set($key, $arr);

            return true;
        endif;
    }

    /**
     * @inheritdoc
     */
    public function values()
    {
        return array_values($this->attributes);
    }
}