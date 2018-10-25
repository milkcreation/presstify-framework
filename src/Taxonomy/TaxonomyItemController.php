<?php

namespace tiFy\Taxonomy;

use tiFy\Contracts\Taxonomy\TaxonomyItemInterface;
use tiFy\Kernel\Parameters\AbstractParametersBag;

class TaxonomyItemController extends AbstractParametersBag implements TaxonomyItemInterface
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
        'public'                => true,
        //'publicly_queryable'    => true,
        //'show_ui'            => true,
        //'show_in_menu'       => true,
        //'show_in_nav_menus'  => false,
        'show_in_rest'          => false,
        // @todo 'rest_base'          => ''
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        //'show_tagcloud'      => false,
        //'show_in_quick_edit' => false,
        'meta_box_cb'           => null,
        'show_admin_column'     => false,
        'description'           => '',
        'hierarchical'          => false,
        // @todo update_count_callback => ''
        'query_var'             => true,
        'rewrite'               => true,
        // @todo 'capabilities'       => [],
        'sort'                  => true,
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification de la taxonomie.
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
                if ($post_types = $this->get('object_type', [])) :
                    $post_types = is_array($post_types) ? $post_types : array_map('trim', explode(',', $post_types));
                    foreach ($post_types as $post_type) :
                        register_taxonomy_for_object_type($this->getName(), $post_type);
                    endforeach;
                endif;
            },
            25
        );

        add_action(
            'admin_init',
            function () {
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) :
                    return;
                elseif (defined('DOING_AJAX') && DOING_AJAX) :
                    return;
                elseif (!$initial_terms = $this->get('initial_terms')) :
                    return;
                endif;

                $initial_terms = is_array($initial_terms)
                    ? $initial_terms
                    : array_map(
                        'trim',
                        explode(',', $initial_terms)
                    );

                foreach ($initial_terms as $terms) :
                    foreach ($terms as $slug => $name) :
                        if (!$term = get_term_by('slug', $slug, $taxonomy)) :
                            wp_insert_term($name, $taxonomy, ['slug' => $slug]);
                        endif;
                    endforeach;
                endforeach;
            }
        );

        $this->register();
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
            $this->get('label', _x($this->getName(), 'taxonomy general name', 'tify'))
        );

        $this->set(
            'plural',
            $this->get('plural', $this->get('label'))
        );

        $this->set(
            'singular',
            $this->get('singular', $this->get('label'))
        );

        $this->set(
            'gender',
            $this->get('gender', false)
        );

        $this->set(
            'labels',
            app()
                ->resolve(
                    TaxonomyItemLabelsController::class,
                    [
                        $this->get('label'),
                        array_merge(
                            [
                                'singular' => $this->get('singular'),
                                'plural'   => $this->get('plural'),
                                'gender'   => $this->get('gender'),
                            ],
                            $this->get('labels', [])
                        ),
                    ]
                )
                ->all()
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

        $this->set(
            'show_tagcloud',
            $this->has('show_tagcloud')
                ? $this->get('show_tagcloud')
                : $this->get('show_ui')
        );

        $this->set(
            'show_in_quick_edit',
            $this->has('show_in_quick_edit')
                ? $this->get('show_in_quick_edit')
                : $this->get('show_ui')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        global $wp_taxonomies;

        if (!isset($wp_taxonomies[$this->getName()])) :
            return register_taxonomy($this->getName(), $this->get('object_type', []), $this->all());
        else :
            return $wp_taxonomies[$this->getName()];
        endif;
    }
}