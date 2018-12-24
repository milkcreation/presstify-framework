<?php

namespace tiFy\Field\SelectJs;

use tiFy\Field\FieldController;

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
     *      @var array $choices Liste des choix de selection disponibles. La récupération Ajax doit être inactive.
     *      @var string $choices_cb Classe de traitement de la liste des choix.
     *      @var boolean|array $ajax Activation ou liste des attributs de requête de récupération Ajax des élèments.
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
        'choices_cb'   => SelectJsChoices::class,
        'ajax'         => false,
        //@todo 'autocomplete' => false,
        //@todo 'disabled'     => false,
        'multiple'     => false,
        'removable'    => true,
        'max'          => -1,
        'sortable'     => false,
        'trigger'      => true,
        'picker'       => [],
        'viewer'       => [],
        'classes'      => []
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
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.data-control', 'select-js');

        $this->set('attrs.data-id', $this->getId());

        $this->set(
            'handler',
            [
                'name'      => $this->getName(),
                'disabled'  => $this->get('disabled'),
                'removable' => $this->get('removable'),
                'multiple'  => $this->get('multiple'),
                'attrs'     => [
                    'class'        => '',
                    'data-control' => 'select-js.handler',
                ],
                'choices'   => [],
            ]
        );

        $classes = [
            'autocompleteInput'   => 'FieldSelectJs-autocomplete',
            'handler'             => 'FieldSelectJs-handler',
            'picker'              => 'FieldSelectJs-picker',
            'pickerFilter'        => 'FieldSelectJs-pickerFilter',
            'pickerLoader'        => 'FieldSelectJs-pickerLoader',
            'pickerItem'          => 'FieldSelectJs-pickerItem',
            'pickerItems'         => 'FieldSelectJs-pickerItems',
            'pickerMore'          => 'FieldSelectJs-pickerMore',
            'selection'           => 'FieldSelectJs-selection',
            'selectionItem'       => 'FieldSelectJs-selectionItem',
            'selectionItemRemove' => 'FieldSelectJs-selectionItemRemove',
            'selectionItemSort'   => 'FieldSelectJs-selectionItemSort',
            'trigger'             => 'FieldSelectJs-trigger',
            'triggerHandler'      => 'FieldSelectJs-triggerHandler',
        ];
        foreach($classes as $key => &$class) :
            $class = sprintf($this->get("classes.{$key}", '%s'), $class);
        endforeach;
        $this->set('classes', $classes);

        $choices_cb = $this->get('choices_cb');

        $this->set(
            'datas.options',
            [
                'autocomplete' => (bool)$this->get('autocomplete'),
                'classes'      => $this->get('classes', []),
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
                'source'       => ($this->get('ajax') === false)
                    ? false
                    : array_merge(
                        [
                            'action' => 'field_select_js',
                            'args'   => [
                                'page'     => 1,
                                'per_page' => 20,
                                'in'       => [],
                                'not_in'   => []
                            ]
                        ],
                        is_array($this->get('ajax')) ? $this->get('ajax') : [],
                        [
                            '_ajax_nonce' => wp_create_nonce('FieldSelectJs' . $this->getId()),
                            '_id'         => $this->getId(),
                            '_viewer'     => $this->get('viewer', []),
                            '_choices_cb' => $choices_cb
                        ]
                    ),
                'trigger'      => $this->get('trigger', []),
                'errors'       => [
                    'max_attempt' => __('Le nombre maximum de valeurs autorisées est atteint.', 'tify')
                ]
            ]
        );

        if ($this->get('datas.options.source')) :
            $items = new $choices_cb(
                params($this->get('datas.options.source.args', [])),
                $this->viewer(),
                $this->getValue()
            );
        else :
            $choices = $this->get('choices', []);
            $items = ($choices instanceof SelectJsChoices)
                ? $choices
                : new $choices_cb($this->get('choices', []), $this->viewer(), $this->getValue());
        endif;
        /** @var SelectJsChoices $items */
        $this->set('datas.items', (array)$items->all());

        $this->set('attrs.class', trim($this->get('attrs.class') . ' FieldSelectJs'));
        $this->pull('attrs.name');
        $this->pull('attrs.value');
    }

    /**
     * {@inheritdoc}
     */
    protected function parseDefaults()
    {
        $this->parseName();
        $this->parseValue();

        foreach($this->get('view', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
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

        $choices_cb = request()->post('_choices_cb');
        /** @var SelectJsChoices $items */
        $items = new $choices_cb(params(request()->post('args', [])), $this->viewer());

        wp_send_json($items->all());
    }
}