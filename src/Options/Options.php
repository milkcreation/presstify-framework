<?php

namespace tiFy\Options;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;
use tiFy\TabMetabox\TabMetabox;

class Options extends AppController
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *
     *      @var string $hookname Identifiant de qualification de la page d'accroche d'affichage.
     *      @var string $menu_slug Identifiant de qualification du menu.
     *      @var string $cap Habilitation d'accès.
     *      @var string $page_title Intitulé global de la page.
     *      @var string $menu_title Intitulé global de l'entrée de menu.
     *      @var array $admin_page Attributs de configuration de la page des options.
     *      @var array $admin_bar Attributs de configuration de la barre d'administration.
     *      @var array $box Attributs de configuration de la boite à onglet.
     *      @var array $nodes Liste des greffons.
     *      @var string $render Style d'affichage de la page (standard|metaboxes|@todo méthode personnalisée|@todo function personnalisée).
     * }
     */
    protected $attributes = [
        'hookname'          => 'settings_page_tify_options',
        'menu_slug'         => 'tify_options',
        'cap'               => 'manage_options',
        'page_title'        => '',
        'menu_title'        => '',
        'admin_page'        => [],
        'admin_bar'         => [],
        'box'               => [],
        'nodes'             => [],
        'render'            => 'metaboxes'
    ];

    /**
     * Traitement des attributs de configuration de la page des options
     *
     * @return void
     */
    private function _parseAdminPage()
    {
        $this->set(
            'admin_page',
            array_merge(
                [
                    'parent_slug'   => 'options-general.php',
                    'page_title'    => $this->get('page_title'),
                    'menu_title'    => $this->get('menu_title'),
                    'capability'    => $this->get('cap'),
                    'menu_slug'     => $this->get('menu_slug'),
                    'function'      => [$this, 'render'],
                    'icon_url'      => '',
                    'position'      => null
                ],
                $this->get('admin_page', [])
            )
        );
    }

    /**
     * Traitement des attributs de configuration de la barre d'administration.
     *
     * @return void
     */
    private function _parseAdminBar()
    {
        $this->set(
            'admin_bar',
            array_merge(
                [
                    'id'     => $this->get('menu_slug'),
                    'title'  => $this->get('menu_title'),
                    'parent' => 'site-name',
                    'href'   => admin_url('/options-general.php?page=' . $this->get('menu_slug')),
                    'group'  => false,
                    'meta'   => []
                ],
                $this->get('admin_bar', [])
            )
        );
    }

    /**
     * Traitement des attributs de boite à onglet.
     *
     * @return void
     */
    private function _parseBox()
    {
        $this->set(
            'box',
            array_merge(
                [
                    'title'       => $this->get('page_title'),
                ],
                $this->get('box', [])
            )
        );
    }

    /**
     * Traitement de la liste des zones de saisie.
     *
     * @return void
     */
    private function _parseNodes()
    {
        $nodes = [];
        foreach ($this->get('nodes', []) as $attrs) :
            $nodes[] = $attrs;
        endforeach;

        $this->set('nodes', $nodes);
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appTemplates(['directory' => $this->appDirname() . '/templates']);
        $this->appAddAction('init', null, 99999);
    }

    /**
     * Déclaration de menu d'administration de Wordpress.
     *
     * @return void
     */
    public function admin_menu()
    {
        if ($attrs = $this->get('admin_page', [])) :
            if ($attrs['parent_slug']) :
                \add_submenu_page(
                    $attrs['parent_slug'],
                    $attrs['page_title'],
                    $attrs['menu_title'],
                    $attrs['capability'],
                    $attrs['menu_slug'],
                    $attrs['function']
                );
            else :
                \add_menu_page(
                    $attrs['page_title'],
                    $attrs['page_title'],
                    $attrs['capability'],
                    $attrs['menu_slug'],
                    $attrs['function'],
                    $attrs['icon_url'],
                    $attrs['position']
                );
            endif;
        endif;
    }

    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        if (get_current_screen()->id === $this->get('hookname')) :
            \wp_enqueue_style('tiFyOption', $this->appAssetUrl('/Option/css/styles.css'), [], 171030);
        endif;
    }

    /**
     * Modification de la barre d'administration.
     *
     * @param \WP_Admin_Bar $wp_admin_bar
     *
     * @return void
     */
    public function admin_bar_menu(&$wp_admin_bar)
    {
        if (!$this->getNodes() |\is_admin()) :
            return;
        endif;

        if (!$admin_bar = $this->get('admin_bar', [])) :
            return;
        endif;

        $wp_admin_bar->add_node($admin_bar);
    }

    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attributs à récupérer. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Récupération de l'identifiant d'accroche de la page d'affichage
     *
     * @return string
     */
    public function getHookname()
    {
        return $this->get('hookname');
    }

    /**
     * Récupération de la liste des greffons de boite à onglets
     *
     * @return null|array
     */
    public function getNodes()
    {
        return $this->get('nodes');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        $this->attributes['page_title'] = __('Réglages des options du site', 'tify');
        $this->attributes['menu_title'] = __('Options du site', 'tify');

        $this->attributes = array_merge(
            $this->attributes,
            $this->appConfig()
        );

        $this->_parseAdminPage();
        $this->_parseAdminBar();
        $this->_parseBox();
        $this->_parseNodes();

        do_action('tify_options_register', $this);

        if ($this->getNodes()) :
            $this->appAddAction('tify_tabmetabox_register');
            $this->appAddAction('admin_menu');
            $this->appAddAction('admin_enqueue_scripts');
            $this->appAddAction('admin_bar_menu');
        endif;
    }

    /**
     * Déclaration d'une section de boîte à onglets.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function register($attrs = [])
    {
        $this->attributes['nodes'][] = $attrs;
    }

    /**
     * Rendu de l'interface d'administration.
     *
     * return string
     */
    public function render()
    {
        echo $this->appTemplateRender(
            $this->get('render'),
            [
                'page_title'   => $this->get('page_title'),
                'option_group' => $this->get('menu_slug')
            ]
        );
    }

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attributs à définir. Syntaxe à point permise.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        return Arr::set($this->attributes, $key, $value);
    }

    /**
     * Déclaration de la boîte à onglets.
     *
     * @param TabMetabox $tabMetaboxController Classe de rappel du controleur de boite à onglets de controle de saisie.
     *
     * @return void
     */
    public function tify_tabmetabox_register($tabMetaboxController)
    {
        if ($nodes = $this->getNodes()) :
            if ($attrs = $this->get('box')) :
                $tabMetaboxController->registerBox(
                    $this->getHookname(),
                    $attrs
                );
            endif;

            foreach ($nodes as $attrs) :
                $tabMetaboxController->registerNode($this->getHookname(), $attrs);
            endforeach;
        endif;
    }
}