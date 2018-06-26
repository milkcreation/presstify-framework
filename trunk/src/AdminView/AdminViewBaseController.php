<?php

namespace tiFy\AdminView;

use Illuminate\Support\Arr;
use tiFy\AdminView\AdminMenu\AdminMenuBaseController;
use tiFy\AdminView\AdminMenu\AdminMenuInterface;
use tiFy\AdminView\Notice\NoticeCollectionBaseController;
use tiFy\AdminView\Notice\NoticeCollectionInterface;
use tiFy\AdminView\Param\ParamCollectionBaseController;
use tiFy\AdminView\Param\ParamCollectionInterface;
use tiFy\AdminView\Request\RequestBaseController;
use tiFy\AdminView\Request\RequestInterface;
use tiFy\Apps\AppController;
use tiFy\Components\Db\DbPostsController;
use tiFy\Components\Labels\LabelsBaseController;
use tiFy\Components\Labels\LabelsControllerInterface;
use tiFy\Components\Partial\Notice\Notice;

class AdminViewBaseController extends AppController implements AdminViewControllerInterface
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
     * @var AdminViewParamsControllerInterface
     */
    protected $params;

    /**
     * Ecran courant d'affichage de la page.
     * @var null|\WP_Screen
     */
    protected $screen;

    /**
     * Controleur du fournisseur de service.
     * @var string
     */
    protected $serviceProvider = AdminViewServiceProvider::class;

    /**
     * Liste des classes de rappel des services.
     * @var array
     */
    protected $providers = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du controleur.
     * @param array$attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        parent::__construct();

        $this->name = $name;

        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        $this->providers = array_merge(
            [
                'admin_menu' => AdminMenuBaseController::class,
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

        $this->appAddAction('admin_menu', [$this->adminMenu(), 'admin_menu']);
        $this->appAddAction('admin_notices', [$this->notices(), 'admin_notices']);
        $this->appAddAction('current_screen');
    }

    /**
     * Récupération de la classe de rappel du controleur de menu d'administration
     *
     * @return AdminMenuInterface
     */
    public function adminMenu()
    {
        return $this->provide('admin_menu');
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
        $this->appServiceAdd('tify.admin_view.service_provider', new $serviceProviderConcrete([], $this));
        $this->appServiceProvider($this->appServiceGet('tify.admin_view.service_provider'));
    }

    /**
     * Affichage de l'écran courant
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page courante de l'interface d'administration de Wordpress.
     *
     * @return void
     */
    public function current_screen($wp_screen)
    {
        if ($wp_screen->id !== $this->getHookname()) :
            return;
        endif;

        $this->screen = $wp_screen;

        $this->initParams();
        $this->check_user_can();

        if (method_exists($this, 'admin_enqueue_scripts')) :
            $this->appAddAction('admin_enqueue_scripts');
        endif;
        $this->appAddAction('admin_notices');
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
    public function getHookname()
    {
        return $this->get('hookname');
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
    public function getScreen()
    {
        return $this->screen;
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
        return $this->appServiceGet('tify.admin_view.service_provider');
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