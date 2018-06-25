<?php

namespace tiFy\Apps\Attributes;

use Illuminate\Support\Arr;
use tiFy\Apps\AppControllerInterface;

abstract class AbstractAttributesController implements AttributesControllerInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Liste des paramètres.
     * @var array
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des paramètres personnalisés.
     * @param AppControllerInterface $app  Classe de rappel du controleur de l'application.
     *
     * @return void
     */
    public function __construct($attrs = [], AppControllerInterface $app)
    {
        $this->app = $app;

        $attrs = array_merge(
            $this->defaults(),
            $attrs
        );

        $this->parse($attrs);
    }

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
    public function has($key, $default = null)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }
}