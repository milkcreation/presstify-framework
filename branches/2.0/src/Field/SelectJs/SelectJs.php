<?php

namespace tiFy\Field\SelectJs;

use tiFy\Field\Select\SelectChoices;
use tiFy\Field\Select\SelectChoice;
use tiFy\Field\FieldController;
use WP_Query;

class SelectJs extends FieldController
{
    static $ajaxInit = false;

    /**
     * Liste des attributs de configuration.
     *
     * @var array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des attrbuts de balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string|array $value Valeur initiale de soumission du champ.
     *      @var array|SelectChoices|SelectChoice[] $choices Liste des choix de selection disponibles. Si source inactif.
     *      @var boolean|array $source Activation ou liste des attributs de requête de récupération Ajax des élèments.
     *      @todo boolean $autocomplete Activation le champs de selection par autocomplétion.
     *      @var boolean $disabled Activation/Désactivation du controleur de champ.
     *      @var boolean $multiple Activation la selection multiple d'éléments.
     *      @var int $max Nombre d'éléments maximum (multiple uniquement). défaut -1 pas de limite.
     *      @var boolean removable Activation de la suppression des éléments depuis la liste des éléments sélectionnés.
     *                             (multiple uniquement).
     *      @var boolean|array $sortable Activation|Liste des options du contrôleur ajax d'ordonnancement.
     *                                   (multiple uniquement). @see http://jqueryui.com/sortable/
     *      @var boolean trigger Activation de l'affichage de l'interface d'ouverture et de fermeture du selecteur.
     *      @var array picker {
     *          Liste des attributs de configuration de l'interface du selecteur d'éléments.
     *
     *          @todo array $attrs Liste des attributs HTML.
     *          @var string $appendTo Selecteur jQuery de positionnement dans le DOM. défaut body.
     *          @var string $placement Comportement de la liste déroulante. top|bottom|clever. défaut clever (adaptatif).
     *          @var array $delta {
     *              Liste des valeurs d'ajustements de positionnement. Exprimée en px.
     *
     *              @var int $top
     *              @var int $left
     *              @var int $width
     *          }
     *          @var boolean $filter Activation du champ de filtrage des éléments.
     *          @var string $loader Rendu de l'indicateur de préchargement.
     *          @var string $more Rendu de '+'.
     *      }
     * @var array $viewer Liste des attributs de configuration de la classe des gabarits d'affichage.
     * }
     */
    protected $attributes = [
        'before'       => '',
        'after'        => '',
        'attrs'        => [],
        'name'         => '',
        'value'        => null,
        'choices'      => [],
        'source'       => false,
        //@todo 'autocomplete' => false,
        //@todo 'disabled'     => false,
        'multiple'     => false,
        'removable'    => true,
        'max'          => -1,
        'sortable'     => false,
        'trigger'      => true,
        'picker'       => [],
        'viewer'       => []
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

                wp_register_style(
                    'FieldSelectJs',
                    assets()->url('field/select-js/css/styles.css'),
                    [],
                    171218
                );

                wp_register_script(
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
    public function display()
    {
        assets()->setDataJs($this->getId(), $this->get('datas', []));

        return $this->viewer('select-js', $this->all());
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
                    'class'        => '',
                    'data-control' => 'select-js.handler',
                ],
                'choices'   => $this->get('choices'),
            ]
        );

        $this->set('attrs.data-id', $this->getId());

        $this->set(
            'datas.options',
            [
                'autocomplete' => (bool)$this->get('autocomplete'),
                'disabled'     => (bool)$this->get('disabled'),
                'duplicate'    => (bool)$this->get('duplicate'),
                'max'          => (int)$this->get('max'),
                'multiple'     => (bool)$this->get('multiple'),
                'picker'       => array_merge(
                    [
                        'filter' => false,
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
                'source'       => ($this->get('source') === false)
                    ? false
                    : array_merge(
                        [
                            'action'      => 'field_select_js',
                            'query_args'  => [
                                'page'     => 1,
                                'per_page' => 10,
                                'in'       => [],
                                'not_in'   => []
                            ]
                        ],
                        is_array($this->get('source')) ? $this->get('source') : [],
                        [
                            '_ajax_nonce' => wp_create_nonce('FieldSelectJs' . $this->getId()),
                            '_id' => $this->getId(),
                            '_viewer' => $this->get('viewer', []),
                        ]
                    ),
                'trigger'      => $this->get('trigger', []),
                'errors'       => [
                    'max_attempt' => __('Le nombre maximum de valeurs autorisées est atteint.', 'tify')
                ]
            ]
        );

        $this->set(
            'datas.items',
            !$this->get('datas.options.source')
                ? []
                : $this->queryItems(
                array_merge(
                    $this->get('datas.options.source.query_args', []),
                    ['in' => $this->getValue(), 'per_page' => -1]
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
        $value = $this->get('multiple') ? $value : [reset($value)];

        return $value;
    }

    /**
     * Récupération de la liste des résultats via Ajax.
     *
     * @return void
     */
    public function wp_ajax()
    {
        check_ajax_referer('FieldSelectJs' . request()->post('_id'));

        $this->set('viewer', request()->post('_viewer', []));

        wp_send_json($this->queryItems(request()->post('query_args', [])));
    }

    /**
     * Requête de récupération des éléments.
     *
     * @param array $args Arguments de requête de récupération des éléments.
     *
     * @return array
     */
    public function queryItems($args = [])
    {
        $args['post__in'] = $args['post__in'] ?? ($args['in'] ?? []);
        $args['post__not_in'] = $args['post__not_in'] ?? ($args['not_in'] ?? []);
        $args['posts_per_page'] = $args['posts_per_page'] ?? ($args['per_page'] ?? 2);
        $args['paged'] = $args['page'] ?? 1;
        if (!empty($args['term'])) :
            $args['s'] = $args['term'];
        endif;
        $args['post_type'] = $args['post_type'] ?? 'any';

        unset($args['in'], $args['not_in'], $args['per_page'], $args['page'], $args['term']);

        $items = [];
        $wp_query = new WP_Query($args);
        if ($wp_query->have_posts()) :
            while ($wp_query->have_posts()) : $wp_query->the_post();
                global $post;

                $item = ['value' => get_the_ID(), 'content' => get_the_title()];
                $item['picker'] = (string)$this->viewer('picker', compact('item', 'post'));
                $item['selection'] = (string)$this->viewer('selected', compact('item', 'post'));

                $items[] = $item;
            endwhile;
        endif;
        wp_reset_query();

        return $items;
    }
}