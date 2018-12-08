<?php

namespace tiFy\Field\SelectJs;

use tiFy\Field\FieldController;
use tiFy\Field\FieldOptionsItemController;
use WP_Query;

class SelectJs extends FieldController
{
    /**
     * Liste des attributs de configuration.
     *
     * @var array                              $attributes   {
     * @var string                             $before       Contenu placé avant le champ.
     * @var string                             $after        Contenu placé après le champ.
     * @var string                             $name         Attribut de configuration de la qualification de
     *                                                            soumission du champ "name".
     * @var string|array                       $value        Attribut de configuration de la valeur initiale de
     *      soumission du champ "value".
     * @var array|FieldOptionsItemController[] $options      Liste des choix de selection disponibles.
     * @var array                              $source       Liste des attributs de requête de récupération des
     *      élèments.
     * @var bool                               $disabled     Activation/Désactivation du controleur de champ.
     * @var bool                               $removable    Activation/Désactivation de la suppression d'un élément
     *      dans la liste des éléments sélectionné.
     * @var bool                               $multiple     Autorise la selection multiple d'éléments.
     * @var bool                               $duplicate    Autorise les doublons dans la liste de selection (multiple
     *      actif doit être actif).
     * @var bool                               $autocomplete Active le champs de selection par autocomplétion.
     * @var int                                $max          Nombre d'élément maximum @todo.
     * @var array                              $sortable     {
     *          Liste des options du contrôleur ajax d'ordonnancement.
     * @see http://jqueryui.com/sortable/
     *      }
     * @var array trigger {
     *          Liste des attributs de configuration de l'interface d'action.
     *
     * @var string                             $class        Classes HTML de l'élément.
     * @var bool                               $arrow        Affichage de la fléche de selection.
     *      }
     * @var array picker {
     *          Liste des attributs de configuration de l'interface de selection des éléments.
     *
     * @var string                             $class        Classes HTML de l'élément.
     * @var string                             $appendTo     Selecteur jQuery de positionnement dans le DOM. défaut
     *      body.
     * @var string                             $placement    Comportement de la liste déroulante. top|bottom|clever.
     *      défaut clever adaptatif.
     * @var array                              $delta        {
     *
     * @var int                                $top
     * @var int                                $left
     * @var int                                $width
     *          }
     * @var bool                               $adminbar     Gestion de la barre d'administration Wordpress. défaut
     *      true.
     * @var bool                               $filter       Champ de filtrage des éléments de la liste de selection.
     * @var string                             $loader       Rendu de l'indicateur de préchargement.
     * @var string                             $more         Rendu de '+'.
     *      }
     * @var array                              $viewer       Liste des attributs de configuration de la classe des
     *      gabarits d'affichage.
     * }
     */
    protected $attributes = [
        'before'       => '',
        'after'        => '',
        'attrs'        => [],
        'name'         => '',
        'value'        => null,
        'options'      => [],
        'source'       => false,
        'disabled'     => false,
        'removable'    => true,
        'multiple'     => false,
        'duplicate'    => false,
        'sortable'     => false,
        'autocomplete' => false,
        'max'          => -1,
        'trigger'      => [],
        'picker'       => [],
        'viewer'       => [],
        'controller'   => []
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                add_action(
                    'wp_ajax_field_select_js',
                    [$this, 'wp_ajax']
                );

                add_action(
                    'wp_ajax_nopriv_field_select_js',
                    [$this, 'wp_ajax']
                );

                \wp_register_style(
                    'FieldSelectJs',
                    assets()->url('field/select-js/css/styles.css'),
                    [],
                    171218
                );

                \wp_register_script(
                    'FieldSelectJs',
                    assets()->url('field/select-js/js/scripts.js'),
                    ['jquery-ui-widget', 'jquery-ui-sortable'],
                    171218,
                    true
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        partial('spinner')->enqueue_scripts('three-bounce');
        wp_enqueue_style('FieldSelectJs');
        wp_enqueue_script('FieldSelectJs');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->parseOptions();

        $this->set('attrs.data-control', 'select-js');

        $this->set(
            'handler',
            [
                'name'      => $this->getName(),
                'value'     => $this->getValue(),
                'disabled'  => $this->get('disabled'),
                'removable' => $this->get('removable'),
                'multiple'  => $this->get('multiple'),
                'attrs'     => [
                    'id'           => '',
                    'class'        => '',
                    'data-control' => 'select-js.handler'
                ],
                'options'   => $this->getOptions()
            ]
        );

        $this->set(
            'attrs.data-options',
            rawurlencode(
                json_encode(
                    [
                        'autocomplete' => (bool)$this->get('autocomplete'),
                        'disabled'     => (bool)$this->get('disabled'),
                        'duplicate'    => (bool)$this->get('duplicate'),
                        'max'          => (int)$this->get('max'),
                        'multiple'     => (bool)$this->get('multiple'),
                        'picker'       => array_merge(
                            [
                                'loader' => (string)partial(
                                    'spinner',
                                    [
                                        'attrs'   => [
                                            'id'    => '',
                                            'class' => 'tiFyField-SelectJsPickerSpinkit',
                                        ],
                                        'spinner' => 'three-bounce',
                                    ]
                                ),
                                'more'   => '+',
                            ],
                            $this->get('picker', [])
                        ),
                        'removable'    => (bool)$this->get('removable'),
                        'selected'     => $this->getValue(),
                        'sortable'     => $this->get('sortable'),
                        'source'       => array_merge(
                            [
                                'action'      => 'field_select_js',
                                '_ajax_nonce' => wp_create_nonce('FieldSelectJs' . $this->getId()),
                                'id'          => $this->getId(),
                                'query_args'  => [],
                                'viewer'      => $this->get('viewer', [])
                            ],
                            is_array($this->get('source')) ? $this->get('source') : []
                        ),
                        'trigger'      => $this->get('trigger', [])
                    ],
                    JSON_FORCE_OBJECT
                )
            )
        );
        $this->set('attrs.class', $this->get('attrs.class') . ' FieldSelectJs');
        $this->pull('attrs.name');
        $this->pull('attrs.value');
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $value = $this->get('value', null);

        if (is_null($value)) :
            return null;
        endif;

        $value = is_string($value)
            ? array_map('trim', explode(',', $value))
            : (array)$value;
        $value = $this->get('duplicate') ? $value : array_unique($value);
        $value = $this->get('multiple') ? $value : reset($value);

        return $value;
    }

    /**
     * Récupération de la liste des résultats via Ajax.
     *
     * @return void
     */
    public function wp_ajax()
    {
        check_ajax_referer('FieldSelectJs' . request()->post('id'));

        $this->set('viewer', request()->post('viewer', []));

        wp_send_json($this->queryItems(request()->post('query_args', [])));
    }

    /**
     * Requête de récupération des éléments.
     *
     * @param array $query_args Arguments de requête de récupération des éléments.
     *
     * @return array
     */
    public function queryItems($query_args = [])
    {
        $query_args['post__in']     = $query_args['in'] ?? [];
        $query_args['post__not_in'] = $query_args['not'] ?? [];
        $query_args['post_type']    = $query_args['post_type'] ?? 'any';
        $query_args['paged']        = $query_args['page'] ?? 1;
        unset($query_args['page']);

        $items    = [];
        $wp_query = new WP_Query($query_args);
        if ($wp_query->have_posts()) :
            while ($wp_query->have_posts()) : $wp_query->the_post();
                global $post;

                $item = ['value' => get_the_ID(), 'content' => get_the_title()];
                $item['picker']    = (string)$this->viewer('picker', compact('item', 'post'));
                $item['selection'] = (string)$this->viewer('selected', compact('item', 'post'));

                $items[] = $item;
            endwhile;
        endif;
        wp_reset_query();

        return $items;
    }
}