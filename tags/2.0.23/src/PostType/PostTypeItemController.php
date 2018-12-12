<?php

namespace tiFy\PostType;

use tiFy\Contracts\PostType\PostTypeItemInterface;
use tiFy\Kernel\Params\ParamsBag;

class PostTypeItemController extends ParamsBag implements PostTypeItemInterface
{
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
        //'exclude_from_search'   => false,
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
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);

        add_action(
            'init',
            function () {
                if ($taxonomies = $this->get('taxonomies', [])) :
                    foreach ($taxonomies as $taxonomy) :
                        register_taxonomy_for_object_type($taxonomy, $this->getName());
                    endforeach;
                endif;
            },
            25
        );

        $this->register();
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'rewrite' => [
                'slug'       => $this->getName(),
                'with_front' => false,
                'feeds'      => true,
                'pages'      => true,
                'ep_mask'    => EP_PERMALINK,
            ],
            'rest_base' => $this->getName()
        ];
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
    public function parse($attrs = [])
    {
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
            app()
                ->resolve(
                    PostTypeItemLabelsController::class,
                    [
                        $this->get('label'),
                        array_merge(
                            [
                                'singular' => $this->get('singular'),
                                'plural'   => $this->get('plural'),
                                'gender'   => $this->get('gender'),
                            ],
                            (array)$this->get('labels', [])
                        )
                    ]
                )
                ->all()
        );

        $this->set(
            'exclude_from_search',
            $this->has('exclude_from_search')
                ? $this->get('exclude_from_search')
                : !$this->get('public')
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

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        global $wp_post_types;

        if (!isset($wp_post_types[$this->getName()])) :
            return register_post_type($this->getName(), $this->all());
        else :
            return $wp_post_types[$this->getName()];
        endif;
    }
}