<?php

namespace tiFy\View\Pattern;

use tiFy\Contracts\View\PatternController as PatternControllerContract;
use tiFy\Contracts\View\PatternFactory;
use tiFy\Kernel\Container\Container;

abstract class PatternController extends Container implements PatternControllerContract
{
    /**
     * Instance du constructeur de disposition associée.
     * @var PatternFactory
     */
    protected $factory;

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        PatternServiceProvider::class
    ];

    /**
     * Liste des services fournis.
     * @var array
     */
    protected $providers = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param PatternFactory $factory Instance de la fabrique de disposition associée.
     *
     * @return void
     */
    public function __construct(PatternFactory $factory)
    {
        $this->factory = $factory;

        parent::__construct();

        $this->boot();
    }

    /**
     * Appel de la classe en tant que fonction.
     *
     * @return string
     */
    public function __invoke()
    {
        return $this->render();
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function db()
    {
        return $this->get('db');
    }

    /**
     * {@inheritdoc}
     */
    public function extend($abstract)
    {
        return parent::extend("view.pattern.{$this->name()}.{$abstract}");
    }

    /**
     * {@inheritdoc}
     */
    public function factory()
    {
        return $this->factory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($abstract, array $args = [])
    {
        return parent::get("view.pattern.{$this->name()}.{$abstract}", $args);
    }

    /**
     * {@inheritdoc}
     */
    public function has($abstract)
    {
        return parent::has("view.pattern.{$this->name()}.{$abstract}");
    }

    /**
     * {@inheritdoc}
     */
    public function label($key = null, $default = '')
    {
        $labels = $this->get('labels');

        if (is_null($key)) :
            return $labels;
        endif;

        return $labels->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $this->process();
        $this->prepare();
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return $this->factory()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function notices()
    {
        return $this->get('notices');
    }

    /**
     * {@inheritdoc}
     */
    public function param($key = null, $default = null)
    {
        $params = $this->get('params');

        if (is_null($key)) :
            return $params;
        endif;

        return $params->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return __('Aucun contenu à afficher', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function request()
    {
        return $this->get('request');
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        $viewer = $this->get('viewer');

        if (func_num_args() === 0) :
            return $viewer;
        endif;

        return $viewer->make("_override::{$view}", $data);
    }
}