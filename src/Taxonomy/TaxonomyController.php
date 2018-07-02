<?php

namespace tiFy\Taxonomy;

use tiFy\Apps\Attributes\AbstractAttributesController;

class TaxonomyController extends AbstractAttributesController
{
    /**
     * Classe de rappel du controleur de gestion des types de post.
     * @return Taxonomy
     */
    protected $app;

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
     * @param string $name Nom de qualification de la taxonomy.
     * @param array $attrs Attribut de configuration.
     * @param Taxonomy $app Classe de rappel du controleur de gestion des taxonomies.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], Taxonomy $app)
    {
        $this->name = $name;

        parent::__construct($attrs, $app);

        \register_taxonomy(
            $name,
            $this->get('object_type', []),
            $this->attributes
        );

        $this->app->appAddAction('init', [$this, 'init'], 25);
        $this->app->appAddAction('admin_init', [$this, 'admin_init']);
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
     * Récupération du nom de qualification du type de post
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
        if ($post_types = $this->get('object_type', [])) :
            $post_types = is_array($post_types) ? $post_types : array_map('trim', explode(',', $post_types));
            foreach ($post_types as $post_type) :
                \register_taxonomy_for_object_type($this->getName(), $post_type);
            endforeach;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('label', $this->get('label', _x($this->getName(), 'taxonomy general name', 'tify')));

        $this->set('plural', $this->get('plural', $this->get('label')));

        $this->set('singular', $this->get('singular', $this->get('label')));

        $this->set('gender', $this->get('gender', false));

        $this->set(
            'labels',
            (new TaxonomyLabelsController(
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
                )
            )->all()
        );

        $this->set('publicly_queryable', $this->has('publicly_queryable') ? $this->get('publicly_queryable') : $this->get('public'));

        $this->set('show_ui', $this->has('show_ui') ? $this->get('show_ui') : $this->get('public'));

        $this->set('show_in_nav_menus', $this->has('show_in_nav_menus') ? $this->get('show_in_nav_menus') : $this->get('public'));

        $this->set('show_in_menu', $this->has('show_in_menu') ? $this->get('show_in_menu') : $this->get('show_ui'));

        $this->set('show_in_admin_bar', $this->has('show_in_admin_bar') ? $this->get('show_in_admin_bar') : $this->get('show_in_menu'));

        $this->set('show_tagcloud', $this->has('show_tagcloud') ? $this->get('show_tagcloud') : $this->get('show_ui'));

        $this->set('show_in_quick_edit', $this->has('show_in_quick_edit') ? $this->get('show_in_quick_edit') : $this->get('show_ui'));
    }
}