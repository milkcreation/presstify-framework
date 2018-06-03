<?php

namespace tiFy\PostType;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;
use tiFy\Components\Labels\LabelsPostTypeController;

class PostTypeController extends AppController
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
        //'rest_base'             => ''
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

        $this->parse($attrs);

        \register_post_type($name, $this->all());

        $this->appAddAction('init', null, 25);
    }

    /**
     * Récupération de la liste complète des attributs de configuration.
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
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
     * @param mixed $default Valeur de retour par defaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'index de qualification de l'attribut.
     *
     * @return mixed
     */
    public function has($key)
    {
        return Arr::has($this->attributes, $key);
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
     * @return void
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

        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

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

        $this->set('labels', $this->get('labels', []));

        $this->set(
            'labels',
            (new LabelsPostTypeController(
                $this->get('label'),
                array_merge(
                    [
                        'singular' => $this->get('singular'),
                        'plural'   => $this->get('plural'),
                        'gender'   => $this->get('gender'),
                    ],
                    $this->get('labels')
                )
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

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'index de qualification de l'attribut.
     * @param mixed $value Valeur attribuée.
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        return Arr::set($this->attributes, $key, $value);
    }
}