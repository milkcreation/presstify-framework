<?php

namespace tiFy\AdminView;

use Illuminate\Support\Arr;
use tiFy\AdminView\Traits\ActionTrait;
use tiFy\AdminView\Traits\HelpersTrait;
use tiFy\AdminView\Traits\NoticesTrait;
use tiFy\AdminView\AdminViewMenuController;
use tiFy\AdminView\AdminViewMenuControllerInterface;
use tiFy\AdminView\AdminViewNoticesController;
use tiFy\AdminView\AdminViewNoticesControllerInterface;
use tiFy\AdminView\AdminViewParamsController;
use tiFy\AdminView\AdminViewParamsControllerInterface;
use tiFy\Apps\AppController;
use tiFy\Db\DbControllerInterface;
use tiFy\Components\Db\DbPostsController;
use tiFy\Components\Labels\LabelsBaseController;
use tiFy\Components\Labels\LabelsControllerInterface;

class AdminViewBaseController extends AppController implements AdminViewControllerInterface
{
    use ActionTrait, HelpersTrait, NoticesTrait;

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
     * Classe de rappel de traitement du menu d'administration.
     * @var AdminViewMenuControllerInterface
     */
    protected $menu;

    /**
     * Classe de rappel de traitement des message de notification.
     * @var AdminViewNoticesControllerInterface
     */
    protected $notices;

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

        $this->attributes = $attrs;

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

        $this->menu = new AdminViewMenuController($this->get('admin_menu', []), $this);
        $this->notices = new AdminViewNoticesController($this->get('notices', []), $this);
        $this->params = new AdminViewParamsController($this->get('params', []), $this);
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
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Récupération de la classe de rappel de l'object base de données
     *
     * @return null|DbControllerInterface
     */
    public function getDb()
    {
        return $this->get('db', null);
    }

    /**
     * Récupération du nom de qualification d'accroche de la page d'affichage de l'interface.
     *
     * @return string
     */
    public function getHookname()
    {
        return $this->get('hookname');
    }

    /**
     * Récupération d'un intitulé.
     *
     * @param string $key Clé d'indexe de l'intitulé.
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function getLabel($key, $default = '')
    {
        /** @var  LabelsControllerInterface $labels */
        $labels = $this->get('labels');

        return $labels->get($key, $default);
    }

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Récupération du nom de qualification du controleur.
     *
     * @return string
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * Récupération de la liste des classes de rappel des gabarits de traitement externe
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
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return mixed
     */
    public function has($key, $default = null)
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
     * Récupération de la classe de rappel du controleur de gestion des paramètres.
     * @var AdminViewParamsControllerInterface
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        return Arr::set($this->attributes, $key, $value);
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function render()
    {
        _e('Aucun contenu à afficher', 'tify');
    }
}