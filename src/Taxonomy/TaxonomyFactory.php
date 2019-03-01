<?php

namespace tiFy\Taxonomy;

use tiFy\Contracts\Taxonomy\TaxonomyFactory as TaxonomyFactoryContract;
use tiFy\Kernel\Params\ParamsBag;

class TaxonomyFactory extends ParamsBag implements TaxonomyFactoryContract
{
    /**
     * Nom de qualification de l'élément.
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
     * @param string $name Nom de qualification de l'élément.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);

        events()->trigger('taxonomy.factory.boot', [&$this]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function label(string $key, string $default = '') : string
    {
        return $this->get("labels.{$key}", $default);
    }

    /**
     * @inheritdoc
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set(
            'label',
            $this->get('label', _x($this->getName(), 'taxonomy general name', 'tify'))
        );

        $this->set('plural', $this->get('plural', $this->get('label')));

        $this->set('singular', $this->get('singular', $this->get('label')));

        $this->set('gender', $this->get('gender', false));

        $labels =  (new TaxonomyLabelsBag(
            $this->get('label'),
            array_merge(
                [
                    'singular' => $this->get('singular'),
                    'plural'   => $this->get('plural'),
                    'gender'   => $this->get('gender'),
                ],
                (array)$this->get('labels', [])
            )
        ));
        $this->set('labels', $labels->all());

        $this->set(
            'publicly_queryable',
            $this->has('publicly_queryable')
                ? $this->get('publicly_queryable')
                : $this->get('public')
        );

        $this->set('show_ui', $this->has('show_ui') ? $this->get('show_ui') : $this->get('public'));

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
}