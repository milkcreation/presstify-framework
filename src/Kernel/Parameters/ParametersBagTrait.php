<?php

namespace tiFy\Kernel\Parameters;

use Illuminate\Support\Arr;

trait ParametersBagTrait
{
    /**
     * Liste des paramètres.
     * @var array
     */
    protected $attributes = [];

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return array_keys($this->attributes);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function pull($key, $default = null)
    {
        return Arr::pull($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function set($key, $value = null)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function values()
    {
        return array_values($this->attributes);
    }
}