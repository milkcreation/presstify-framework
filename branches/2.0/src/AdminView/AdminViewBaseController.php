<?php

namespace tiFy\AdminView;

use Illuminate\Support\Arr;
use tiFy\AdminView\AdminMenu\AdminMenuController;
use tiFy\AdminView\AdminMenu\AdminMenuInterface;
use tiFy\AdminView\Notice\NoticeCollectionController;
use tiFy\AdminView\Notice\NoticeCollectionInterface;
use tiFy\AdminView\Param\ParamCollectionBaseController;
use tiFy\AdminView\Param\ParamCollectionInterface;
use tiFy\Apps\AppController;
use tiFy\Components\Db\DbPostsController;
use tiFy\Components\Labels\LabelsBaseController;
use tiFy\Components\Labels\LabelsControllerInterface;
use tiFy\Components\Partial\Notice\Notice;

class AdminViewBaseController extends AppController implements AdminViewInterface
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
     * Ecran courant d'affichage de la page
     * @var null|\WP_Screen
     */
    protected $screen;

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
                'admin_menu' => AdminMenuController::class,
                'notices'    => NoticeCollectionController::class,
                'params'     => ParamCollectionBaseController::class
            ],
            $this->providers
        );

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;

        $this->appAddAction('current_screen');
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->init();

        if ($admin_menu = $this->getConcrete('admin_menu')) :
            $this->appServiceAdd(
                AdminMenuInterface::class,
                new $admin_menu($this->get('admin_menu', []), $this)
            );
        endif;
        /*if ($notices = $this->getConcrete('notices')) :
            $this->appServiceAdd(
                NoticeCollectionInterface::class,
                new $notices($this->get('notices', []), $this)
            );
        endif;*/
        if ($params = $this->getConcrete('params')) :
            $this->appServiceAdd(
                ParamCollectionInterface::class,
                new $params($this->get('params', []), $this)
            );
        endif;
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
        return Arr::get($this->providers, $key);
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
     * Récupération de la liste des classes de rappel des gabarits de traitement externe.
     *
     * @return array|\tiFy\Core\Ui\Admin\Factory[]
     */
    public function getHandleList()
    {
        if (!$handle_templates = $this->get('handle')) :
            return [];
        endif;

        $handle = [];
        foreach ($handle_templates as $task => $id) :
            if ($factory = Ui::getAdmin($id)) :
                $handle[$task] = $factory;
            endif;
        endforeach;

        return $handle;
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
        // Traitement de la liste des attributs de configuration
        $db = $this->get('db');
        if ($db instanceof DbControllerInterface) :
        else :
            $db = new DbPostsController($this->getName());
        endif;
        $this->set('db', $db);

        $labels = $this->get('labels');
        if ($labels instanceof \tiFy\Core\Labels\Factory) :
        elseif (is_string($labels)) :
            $labels = new LabelsBaseController($labels);
        elseif (is_array($labels)) :
            $labels = new LabelsBaseController($this->getName(), $attrs['labels']);
        else :
            $labels = new LabelsBaseController($this->getName());
        endif;
        $this->set('labels', $labels);

        if ($admin_menu = $this->get('admin_menu')) :
            $this->set(
                'admin_menu',
                array_merge(
                    [
                        'menu_slug'   => $this->getName(),
                        'parent_slug' => '',
                        'page_title'  => $this->getName(),
                        'menu_title'  => $this->getName(),
                        'capability'  => 'manage_options',
                        'icon_url'    => null,
                        'position'    => null,
                        'function'    => [$this, 'render'],
                    ],
                    $admin_menu
                )
            );
        endif;
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
        return $this->appServiceGet(ParamCollectionInterface::class);
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
    public function render()
    {
        _e('Aucun contenu à afficher', 'tify');
    }
}