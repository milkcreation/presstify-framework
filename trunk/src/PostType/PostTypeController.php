<?php

namespace tiFy\PostType;

use tiFy\Apps\AppTrait;
use \tiFy\Core\Label\Label;

class PostTypeController extends AppController
{
    use AppTrait;

    /**
     * Nom de qualification du type de post
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de post par defaut.
     * @var array
     */
    protected $defaults = [
        'description'           => '',
        'public'                => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_nav_menus'     => true,
        'show_in_menu'          => true,
        'show_in_admin_bar'     => true,
        'menu_position'         => null,
        'menu_icon'             => false,
        'capability_type'       => 'page',
        'map_meta_cap'          => null,
        'hierarchical'          => false,
        'supports'              => ['title', 'editor', 'thumbnail'],
        'register_meta_box_cb'  => '',
        'taxonomies'            => [],
        'has_archive'           => true,
        'permalink_epmask'      => EP_PERMALINK,
        'query_var'             => true,
        'can_export'            => true,
        'show_in_rest'          => true,
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    ];

    /**
     * Liste des attributs de configuration
     * @var array
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du type de post
     * @param array $attrs Attribut de configuration
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name  = $name;

        $this->defaults['rewrite'] = [
            'slug'       => $post_type,
            'with_front' => false,
            'feeds'      => true,
            'pages'      => true,
            'ep_mask'    => EP_PERMALINK,
        ];
        $this->defaults['rest_base'] = $post_type;

        $this->attributes = $this->parse($attrs);

        \register_post_type(
            $name,
            $this->attributes
        );
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @return array
     */
    public function parse($attrs)
    {
        $label = _x($this->getName(), 'post type general name', 'tify');
        $plural = _x($this->getName(), 'post type plural name', 'tify');
        $singular = _x($this->getName(), 'post type singular name', 'tify');
        $gender = false;

        foreach (['gender', 'label', 'plural', 'singular'] as $key) :
            if (isset($attrs[$key])) :
                ${$key} = $attrs[$key];
                unset($attrs[$key]);
            endif;
        endforeach;

        if (!isset($attrs['labels'])) :
            $attrs['labels'] = [];
        endif;

        $attrs = Labels::register(
            '_tiFyCustomType-post--' . $post_type,
            \wp_parse_args(
                $args['labels'],
                [
                    'singular' => $singular,
                    'plural'   => $plural,
                    'gender'   => $gender,
                ]
            )
        );
        $args['labels'] = $labels->get();

        // Définition des arguments du type de post


        $_args = array_merge($defaults, $args);

        if (!isset($args['publicly_queryable'])) :
            $_args['publicly_queryable'] = $_args['public'];
        endif;

        if (!isset($args['show_ui'])) :
            $_args['show_ui'] = $_args['public'];
        endif;

        if (!isset($args['show_in_nav_menus'])) :
            $_args['show_in_nav_menus'] = $_args['public'];
        endif;

        if (!isset($args['show_in_menu'])) :
            $_args['show_in_menu'] = $_args['show_ui'];
        endif;

        if (!isset($args['show_in_admin_bar'])) :
            $_args['show_in_admin_bar'] = $_args['show_in_menu'];
        endif;

        return $_args;
    }

    /**
     * Récupération du nom de qualification du type de post
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}