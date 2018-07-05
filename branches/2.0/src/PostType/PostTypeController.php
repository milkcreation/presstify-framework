<?php

namespace tiFy\PostType;

use tiFy\Apps\Item\AbstractAppItemController;

class PostTypeController extends AbstractAppItemController
{
    /**
     * Classe de rappel du controleur de gestion des types de post.
     * @return PostType
     */
    protected $app;

    /**
     * Nom de qualification du type de post.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [
        //'label'              => '',
        //'labels'             => '',
        'description'           => '',
        'public'                => true,
        // @todo 'exclude_from_search'   => false,
        //'publicly_queryable'    => true,
        //'show_ui'               => true,
        //'show_in_nav_menus'     => true,
        //'show_in_menu'          => true,
        //'show_in_admin_bar'     => true,
        'menu_position'         => null,
        'menu_icon'             => null,
        'capability_type'       => 'post',
        // @todo capabilities   => [],
        'map_meta_cap'          => null,
        'hierarchical'          => false,
        'supports'              => ['title', 'editor'],
        // @todo 'register_meta_box_cb'  => '',
        'taxonomies'            => [],
        'has_archive'           => false,
        'rewrite'               => true,
        'permalink_epmask'      => EP_PERMALINK,
        'query_var'             => true,
        'can_export'            => true,
        'delete_with_user'      => null,
        'show_in_rest'          => false,
        'rest_base'             => '',
        'rest_controller_class' => 'WP_REST_Posts_Controller'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du type de post.
     * @param array $attrs Attribut de configuration.
     * @param PostType $app Classe de rappel du controleur de gestion des types de post.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], $app)
    {
        $this->name = $name;

        parent::__construct($attrs, $app);

        \register_post_type($name, $this->all());

        $this->app->appAddAction('init', [$this, 'init'], 25);
    }

    /**
     * Récupération du nom de qualification du type de post.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        if ($taxonomies = $this->get('taxonomies', [])) :
            foreach ($taxonomies as $taxonomy) :
                \register_taxonomy_for_object_type($taxonomy, $this->getName());
            endforeach;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->set(
            'rewrite',
            [
                'slug'       => $this->getName(),
                'with_front' => false,
                'feeds'      => true,
                'pages'      => true,
                'ep_mask'    => EP_PERMALINK,
            ]
        );

        $this->set('rest_base', $this->getName());

        parent::parse($attrs);

        $this->set(
            'label',
            $this->get('label', _x($this->getName(), 'post type general name', 'tify'))
        );

        $this->set(
            'plural',
            $this->get('plural', $this->get('label'))
        );

        $this->set(
            'singular',
            $this->get('singular', $this->get('label'))
        );

        $this->set('gender', $this->get('gender', false));

        $this->set(
            'labels',
            (new PostTypeLabelsController(
                $this->get('label'),
                array_merge(
                    [
                        'singular' => $this->get('singular'),
                        'plural'   => $this->get('plural'),
                        'gender'   => $this->get('gender'),
                    ],
                    $this->get('labels', [])
                ),
                $this->app
            ))->all()
        );

        $this->set(
            'publicly_queryable',
            $this->has('publicly_queryable')
                ? $this->get('publicly_queryable')
                : $this->get('public')
        );

        $this->set(
            'show_ui',
            $this->has('show_ui')
                ? $this->get('show_ui')
                : $this->get('public')
        );

        $this->set(
            'show_in_nav_menus',
            $this->has('show_in_nav_menus')
                ? $this->get('show_in_nav_menus')
                : $this->get('public')
        );

        $this->set(
            'show_in_menu',
            $this->has('show_in_menu')
                ? $this->get('show_in_menu')
                : $this->get('show_ui')
        );

        $this->set(
            'show_in_admin_bar',
            $this->has('show_in_admin_bar')
                ? $this->get('show_in_admin_bar')
                : $this->get('show_in_menu')
        );
    }
}