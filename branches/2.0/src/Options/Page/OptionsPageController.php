<?php

/**
 * @name Options.
 * @desc Gestion des options du site.
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Options\Page;

use Illuminate\Support\Arr;
use tiFy\Contracts\Options\OptionsPageInterface;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Contracts\Views\ViewInterface;
use tiFy\Kernel\Parameters\AbstractParametersBag;
use tiFy\Metabox\Metabox;

class OptionsPageController extends AbstractParametersBag implements OptionsPageInterface
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *
     * @var string $hookname Identifiant de qualification de la page d'accroche d'affichage.
     * @var string $cap Habilitation d'accès à la page.
     * @var string $page_title Intitulé de la page.
     * @var string $menu_title Intitulé de l'entrée de menu.
     * @var array $admin_menu Attributs de configuration de la page des options.
     * @var array $admin_bar Attributs de configuration de la barre d'administration.
     * @var array $items Liste des greffons.
     * }
     */
    protected $attributes = [
        'hookname'              => '',
        'cap'                   => 'manage_options',
        'page_title'            => '',
        'menu_title'            => '',
        'admin_menu'            => [],
        'admin_bar'             => [],
        'items'                 => [],
        'admin_enqueue_scripts' => true,
    ];

    /**
     * Liste des éléments.
     * @var array
     */
    protected $items = [];

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du moteur de gabarits d'affichage.
     * @return ViewsInterface
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);

        add_action(
            'admin_menu',
            function () {

                if ($attrs = $this->get('admin_menu', [])) :
                    if ($attrs['parent_slug']) :
                        add_submenu_page(
                            $attrs['parent_slug'],
                            $attrs['page_title'],
                            $attrs['menu_title'],
                            $attrs['capability'],
                            $attrs['menu_slug'],
                            $attrs['function']
                        );
                    else :
                        add_menu_page(
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
        );

        add_action(
            'admin_enqueue_scripts',
            function () {
                if ((get_current_screen()->id === $this->get('hookname')) && $this->get('admin_enqueue_scripts')) :
                    wp_enqueue_style(
                        'optionsPage',
                        assets()->url('options/css/styles.css'),
                        [],
                        171030
                    );
                endif;
            }
        );

        add_action(
            'admin_bar_menu',
            function (&$wp_admin_bar) {
                if (!$this->items || is_admin()) :
                    return;
                elseif (!$admin_bar = $this->get('admin_bar', [])) :
                    return;
                endif;

                $wp_admin_bar->add_node($admin_bar);
            }, 50
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->display();
    }

    /**
     * {@inheritdoc}
     */
    public function add($attrs = [])
    {
        $this->items[] = $attrs;
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
    public function defaults()
    {
        return [
            'hookname'   => 'settings_page_' . $this->getName(),
            'page_title' => __('Réglages', 'tify'),
            'menu_title' => __('Options du site', 'tify'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        return $this->viewer(
            'options-page',
            $this->all()
        );
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
    public function getItems()
    {
        return $this->items;
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
    public function load(\WP_Screen $wp_current_screen)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('name', $this->getName());
        $this->parseAdminMenu();
        $this->parseAdminBar();
        $this->parseItems();
    }

    /**
     * Traitement des attributs par default de configuration du menu d'administration.
     *
     * @return void
     */
    public function parseAdminMenu()
    {
        $this->set(
            'admin_menu',
            array_merge(
                [
                    'parent_slug' => 'options-general.php',
                    'page_title'  => $this->get('page_title'),
                    'menu_title'  => $this->get('menu_title'),
                    'capability'  => $this->get('cap'),
                    'menu_slug'   => $this->getName(),
                    'function'    => function () {
                        echo $this->display();
                    },
                    'icon_url'    => '',
                    'position'    => null,
                ]
            ),
            $this->get('admin_menu', [])
        );
    }

    /**
     * Traitement des attributs de configuration de la barre d'administration.
     *
     * @return void
     */
    public function parseAdminBar()
    {
        $this->set(
            'admin_bar',
            array_merge(
                [
                    'id'     => $this->getName(),
                    'title'  => $this->get('menu_title'),
                    'parent' => 'site-name',
                    'href'   => admin_url('/options-general.php?page=' . $this->get('admin_menu.menu_slug')),
                    'group'  => false,
                    'meta'   => [],
                ],
                $this->get('admin_bar', [])
            )
        );
    }

    /**
     * Traitement des attributs de configuration de la barre d'administration.
     *
     * @return void
     */
    public function parseItems()
    {
        /** @var Metabox $metabox */
        $metabox = app(Metabox::class);

        foreach($this->get('items', []) as $item) :
            $this->items[] = $item;

            $metabox->add(
                $this->getName() . '@options',
                array_merge(
                    [
                        'context' => 'tab'
                    ],
                    $item
                )
            );
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) :
            $cinfo = class_info($this);
            $default_dir = $cinfo->getDirname() . '/views';
            $this->viewer = view()
                ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                ->setController(OptionsPageView::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : (is_dir($default_dir) ? $default_dir : $cinfo->getDirname())
                )
                ->set('options_page', $this);
        endif;

        if (func_num_args() === 0) :
            return $this->viewer;
        endif;

        return $this->viewer->make("_override::{$view}", $data);
    }
}