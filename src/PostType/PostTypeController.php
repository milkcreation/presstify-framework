<?php

namespace tiFy\PostType;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;
use tiFy\Label\Label;

class PostTypeController extends AppController
{
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
     * Liste des attributs de configuration.
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
        $this->name = $name;

        $this->defaults['rewrite'] = [
            'slug'       => $this->getName(),
            'with_front' => false,
            'feeds'      => true,
            'pages'      => true,
            'ep_mask'    => EP_PERMALINK,
        ];
        $this->defaults['rest_base'] = $this->getName();

        $this->attributes = $this->parse($attrs);

        \register_post_type(
            $name,
            $this->attributes
        );

        $this->appAddAction('init', null, 25);
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
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs personnalisés.
     *
     * @return array
     */
    public function parse($attrs = [])
    {
        $label = _x($this->getName(), 'post type general name', 'tify');
        $plural = _x($this->getName(), 'post type plural name', 'tify');
        $singular = _x($this->getName(), 'post type singular name', 'tify');
        $gender = false;

        if (!isset($attrs['labels'])) :
            $attrs['labels'] = [];
        endif;

        $label = $this->appServiceGet(Label::class)->register(
            'tfy.post_type.' . $this->getName(),
            array_merge(
                compact('singular', 'plural', 'gender'),
                $attrs['labels']
            )
        );
        $attrs['labels'] = $label->all();

        $attrs = array_merge($this->defaults, $attrs);

        if (!isset($attrs['publicly_queryable'])) :
            $attrs['publicly_queryable'] = $attrs['public'];
        endif;

        if (!isset($attrs['show_ui'])) :
            $attrs['show_ui'] = $attrs['public'];
        endif;

        if (!isset($attrs['show_in_nav_menus'])) :
            $attrs['show_in_nav_menus'] = $attrs['public'];
        endif;

        if (!isset($attrs['show_in_menu'])) :
            $attrs['show_in_menu'] = $attrs['show_ui'];
        endif;

        if (!isset($attrs['show_in_admin_bar'])) :
            $attrs['show_in_admin_bar'] = $attrs['show_in_menu'];
        endif;

        if(isset($attrs['taxonomies'])) :
            $attrs['taxonomies'] = is_array($attrs['taxonomies'])
                ? $attrs['taxonomies']
                : array_map('trim', explode(',', $attrs['taxonomies']));
        endif;

        return $attrs;
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
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'index de qualification de l'attribut.
     * @param mixed $default Valeur de retoru par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }
}