<?php

namespace tiFy\Kernel\Layout;

use Illuminate\Support\Arr;
use tiFy\Kernel\Layout\LayoutViewInterface;
use tiFy\Kernel\Layout\Notice\NoticeCollectionBaseController;
use tiFy\Kernel\Layout\Notice\NoticeCollectionInterface;
use tiFy\Kernel\Layout\Param\ParamCollectionBaseController;
use tiFy\Kernel\Layout\Param\ParamCollectionInterface;
use tiFy\Kernel\Layout\Request\RequestBaseController;
use tiFy\Kernel\Layout\Request\RequestInterface;
use tiFy\Apps\AppController;
use tiFy\Components\Db\DbPostsController;
use tiFy\Components\Labels\LabelsBaseController;
use tiFy\Components\Labels\LabelsControllerInterface;
use tiFy\Components\Partial\Notice\Notice;

class LayoutBaseController extends AppController implements LayoutControllerInterface
{
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
     * Liste des classes de rappel des services.
     * @var array
     */
    protected $providers = [];

    /**
     * Controleur du fournisseur de service.
     * @var string
     */
    protected $serviceProvider = LayoutServiceProvider::class;

    /**
     * Controleur de vue.
     * @var LayoutViewInterface
     */
    protected $view;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du controleur.
     * @param array $attrs Liste des attributs de configuration.
     * @param LayoutViewInterface $view Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], $view)
    {
        parent::__construct();

        $this->name = $name;
        $this->view = $view;

        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        $this->providers = array_merge(
            [
                'notices'    => NoticeCollectionBaseController::class,
                'params'     => ParamCollectionBaseController::class,
                'labels'     => LabelsBaseController::class,
                'request'    => RequestBaseController::class
            ],
            $this->providers
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
        $this->init();

        $serviceProviderConcrete = $this->serviceProvider;
        $this->appServiceAdd('tify.layout.service_provider' . $this->getName(), new $serviceProviderConcrete([], $this));
        $this->appServiceProvider($this->appServiceGet('tify.layout.service_provider' . $this->getName()));
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
    public function getConcrete($key, $default = null)
    {
        return Arr::get($this->providers, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getDb()
    {
        return $this->get('db', null);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($key, $default = '')
    {
        /** @var  LabelsControllerInterface $labels */
        $labels = $this->get('labels');

        return $labels->get($key, $default);
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
    public function getView()
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * Initialisation.
     *
     * @return void
     */
    public function init()
    {
        $db = $this->get('db');
        if ($db instanceof DbControllerInterface) :
        else :
            $db = new DbPostsController($this->getName());
        endif;
        $this->set('db', $db);

        $labels = $this->get('labels');
        if ($labels instanceof LabelsControllerInterface) :
        else :
            $labelConcrete = $this->getConcrete('labels');

            if (is_string($labels)) :
                $this->appServiceAdd(LabelsControllerInterface::class, new $labelConcrete($labels));
            elseif (is_array($labels)) :
                $this->appServiceAdd(LabelsControllerInterface::class, new $labelConcrete($this->getName(), $labels));
            else :
                $this->appServiceAdd(LabelsControllerInterface::class, new $labelConcrete($this->getName()));
            endif;

            $labels = $this->appServiceGet(LabelsControllerInterface::class);
        endif;

        $this->set('labels', $labels);
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
    public function provide($key, $args = [])
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
}