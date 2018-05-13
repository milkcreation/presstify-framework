<?php

namespace tiFy\Taxonomy;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;
use tiFy\Label\Label;

class TaxonomyController extends AppController
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
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => false,
        'show_tagcloud'      => false,
        'show_in_quick_edit' => false,
        'meta_box_cb'        => null,
        'show_admin_column'  => true,
        'description'        => '',
        'hierarchical'       => false,
        'query_var'          => true,
        'sort'               => true,
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
            'slug'         => $this->getName(),
            'with_front'   => false,
            'hierarchical' => false,
        ];

        $this->attributes = $this->parse($attrs);

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
        $label = _x($this->getName(), 'taxonomy general name', 'tify');
        $plural = _x($this->getName(), 'taxonomy plural name', 'tify');
        $singular = _x($this->getName(), 'taxonomy singular name', 'tify');
        $gender = false;

        if (!isset($attrs['labels'])) :
            $attrs['labels'] = [];
        endif;

        $label = $this->appServiceGet(Label::class)->register(
            'tfy.taxonomy.' . $this->getName(),
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
}