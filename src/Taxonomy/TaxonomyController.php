<?php

namespace tiFy\Taxonomy;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;
use tiFy\Components\Labels\LabelsTaxonomyController;

class TaxonomyController extends AppController
{
    /**
     * Nom de qualification du type de post
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
        'public'             => true,
        //'publicly_queryable'    => true,
        //'show_ui'            => true,
        //'show_in_menu'       => true,
        //'show_in_nav_menus'  => false,
        'show_in_rest'       => false,
        // @todo 'rest_base'          => ''
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        //'show_tagcloud'      => false,
        //'show_in_quick_edit' => false,
        'meta_box_cb'        => null,
        'show_admin_column'  => false,
        'description'        => '',
        'hierarchical'       => false,
        // @todo update_count_callback => ''
        'query_var'          => true,
        'rewrite'            => true,
        // @todo 'capabilities'       => [],
        'sort'               => true,
    ];

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

        $this->parse($attrs);

        \register_taxonomy(
            $name,
            $this->get('object_type', []),
            $this->attributes
        );

        $this->appAddAction('init', null, 25);
        $this->appAddAction('admin_init');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        if ($post_types = $this->get('object_type', [])) :
            $post_types = is_array($post_types) ? $post_types : array_map('trim', explode(',', $post_types));
            foreach ($post_types as $post_type) :
                \register_taxonomy_for_object_type($this->getName(), $post_type);
            endforeach;
        endif;
    }

    /**
     * Initialisation de l'interface d'administration.
     *
     * @return void
     */
    public function admin_init()
    {
        // Contrôle s'il s'agit d'une routine de sauvegarde automatique.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) :
            return;
        endif;
        // Contrôle s'il s'agit d'une execution de page via ajax.
        if (defined('DOING_AJAX') && DOING_AJAX) :
            return;
        endif;

        if (!$initial_terms = $this->get('initial_terms')) :
            return;
        endif;
        $initial_terms = is_array($initial_terms) ? $initial_terms : array_map('trim', explode(',', $initial_terms));

        foreach ($initial_terms as $terms) :
            foreach ($terms as $slug => $name) :
                if (!$term = get_term_by('slug', $slug, $taxonomy)) :
                    wp_insert_term($name, $taxonomy, ['slug' => $slug]);
                endif;
            endforeach;
        endforeach;
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
        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        $this->set('label', $this->get('label', _x($this->getName(), 'taxonomy general name', 'tify')));
        $this->set('plural', $this->get('plural', $this->get('label')));
        $this->set('singular', $this->get('singular', $this->get('label')));
        $this->set('gender', $this->get('gender', false));
        $this->set('labels', $this->get('labels', []));

        $label = new LabelsTaxonomyController(
            $this->get('label'),
            array_merge(
                [
                    'singular' => $this->get('singular'),
                    'plural'   => $this->get('plural'),
                    'gender'   => $this->get('gender'),
                ],
                $this->get('labels')
            )
        );
        $this->set('labels', $label->all());

        $this->set('publicly_queryable', $this->has('publicly_queryable') ? $this->get('publicly_queryable') : $this->get('public'));

        $this->set('show_ui', $this->has('show_ui') ? $this->get('show_ui') : $this->get('public'));

        $this->set('show_in_nav_menus', $this->has('show_in_nav_menus') ? $this->get('show_in_nav_menus') : $this->get('public'));

        $this->set('show_in_menu', $this->has('show_in_menu') ? $this->get('show_in_menu') : $this->get('show_ui'));

        $this->set('show_in_admin_bar', $this->has('show_in_admin_bar') ? $this->get('show_in_admin_bar') : $this->get('show_in_menu'));

        $this->set('show_tagcloud', $this->has('show_tagcloud') ? $this->get('show_tagcloud') : $this->get('show_ui'));

        $this->set('show_in_quick_edit', $this->has('show_in_quick_edit') ? $this->get('show_in_quick_edit') : $this->get('show_ui'));

        return $attrs;
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
}