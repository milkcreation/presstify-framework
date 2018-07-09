<?php

namespace tiFy\Apps\Layout;

use Illuminate\Support\Arr;
use tiFy\Apps\Layout\LayoutViewInterface;
use tiFy\Apps\Layout\Notice\NoticeCollectionBaseController;
use tiFy\Apps\Layout\Notice\NoticeCollectionInterface;
use tiFy\Apps\Layout\Param\ParamCollectionBaseController;
use tiFy\Apps\Layout\Param\ParamCollectionInterface;
use tiFy\Apps\Layout\Request\RequestBaseController;
use tiFy\Apps\Layout\Request\RequestInterface;
use tiFy\Apps\AppController;
use tiFy\Components\Labels\LabelsBaseController;
use tiFy\Components\Labels\LabelsControllerInterface;
use tiFy\Components\Partial\Notice\Notice;

abstract class AbstractLayoutBaseController extends AppController implements LayoutControllerInterface
{
    /**
     * Classe de rappel du controleur de vue associé.
     * @var LayoutViewInterface
     */
    protected $app;

    /**
     * Nom de qualification du controleur.
     * @var string
     */
    protected $name;

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * Classe de rappel de traitement des paramètres.
     * @var ParamsControllerInterface
     */
    protected $params;

    /**
     * Controleur du fournisseur de service.
     * @var string
     */
    protected $serviceProvider = LayoutServiceProvider::class;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du controleur.
     * @param array $attrs Liste des attributs de configuration.
     * @param LayoutViewInterface $app Classe de rappel du controleur de vue associé.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], $app)
    {
        parent::__construct();

        $this->name = $name;
        $this->app = $app;

        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $serviceProviderConcrete = $this->serviceProvider;
        $this->appServiceAdd('tify.layout.service_provider' . $this->getName(), new $serviceProviderConcrete([], $this));
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $this->process();
        $this->prepare();
    }

    /**
     * {@inheritdoc}
     */
    public function db()
    {
        return $this->provide('db');
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($key, $default = '')
    {
        return $this->labels()->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
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
    public function labels()
    {
        return $this->provide('labels');
    }

    /**
     * {@inheritdoc}
     */
    public function notices()
    {
        return $this->provide('notices');
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
    public function provide($key, $args = null)
    {
        return $this->provider()->get($key, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function provider()
    {
        return $this->appServiceGet('tify.layout.service_provider' . $this->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function param($key, $default = null)
    {
        return $this->params()->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function params()
    {
        return $this->provide('params');
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        return Arr::set($this->attributes, $key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function request()
    {
        return $this->provide('request');
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        _e('Aucun contenu à afficher', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function view()
    {
        return $this->app;
    }
}