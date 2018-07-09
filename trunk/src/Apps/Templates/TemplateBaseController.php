<?php

namespace tiFy\Apps\Templates;

use Illuminate\Support\Arr;
use League\Plates\Template\Template;
use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\Templates\Templates;
use tiFy\Kernel\Tools;

class TemplateBaseController extends Template implements TemplateControllerInterface
{
    /**
     * Classe de rappel de l'application associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Liste des variables passées en argument dans le controleur.
     * @var array
     */
    protected $args = [];

    /**
     * Instance of the template engine.
     * @var Templates
     */
    protected $engine;

    /**
     * CONSTRUCTEUR.
     *
     * @param Engine $engine
     * @param string $name
     * @param array $args Liste des variables passées en argument
     *
     * @return void
     */
    public function __construct(Templates $engine, $name, $args = [], AppControllerInterface $app)
    {
        $this->app = $app;
        $this->args = $args;

        parent::__construct($engine, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getArg($key, $default = null)
    {
        return Arr::get($this->args, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Arr::has($this->data, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function htmlAttrs($attrs)
    {
        return Tools::Html()->parseAttrs($attrs, true);
    }
}