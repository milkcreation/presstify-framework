<?php

namespace tiFy\Field\Fields\SelectJs;

use tiFy\Contracts\Field\SelectJs as SelectJsContract;
use tiFy\Field\FieldController;
use tiFy\Support\ParamsBag;

class SelectJs extends FieldController implements SelectJsContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     *      @var array $choices Liste des choix de selection disponibles. La récupération Ajax doit être inactive.
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
     * }
     */
    protected $attributes = [
        'before'     => '',
        'after'      => '',
        'name'       => '',
        'value'      => null,
        'attrs'      => [],
        'viewer'     => [],
        'choices'    => [],
        'ajax'       => false,
        //@todo 'autocomplete' => false,
        //@todo 'disabled'     => false,
        'multiple'   => false,
        'removable'  => true,
        'max'        => -1,
        'sortable'   => false,
        'trigger'    => true,
        'picker'     => [],
        'classes'    => [],
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
                    asset()->url('field/select-js/css/styles.css'),
                    [],
                    171218
                );

                wp_register_script(
                    'FieldSelectJs',
                    asset()->url('field/select-js/js/scripts.js'),
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

        $this->set('attrs.class', trim(sprintf($this->get('attrs.class', '%s'), ' FieldSelectJs')));
        $this->set('attrs.data-control', 'select-js');
        $this->set('attrs.data-id', $this->getId());

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

        $choices = $this->get('choices', []);
        if (!$choices instanceof SelectJsChoices) :
            if ($args = $this->get('ajax.args', [])) :
                $choices = ParamsBag::createFromAttrs($args);
            endif;

            $choices = new SelectJsChoices($choices, $this->getValue());
        endif;
        $this->set('choices', $choices->setField($this));

        $this->set(
            'datas.options',
            [
                'ajax'       => ($this->get('ajax') === false)
                    ? false
                    : array_merge(
                        [
                            'url'    => admin_url('admin-ajax.php', 'relative'),
                            'data'   => [
                                'action' => 'field_select_js',
                                '_ajax_nonce' => wp_create_nonce('FieldSelectJs' . $this->getId()),
                                '_id'         => $this->getId(),
                                '_viewer'     => $this->get('viewer', []),
                                '_choices_cb' => class_info($choices)->getName(),
                                'args'   => []
                            ],
                            'method' => 'post',
                        ],
                        is_array($this->get('ajax')) ? $this->get('ajax') : []
                    ),
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
                'trigger'      => $this->get('trigger', []),
                'errors'       => [
                    'max_attempt' => __('Le nombre maximum de valeurs autorisées est atteint.', 'tify')
                ]
            ]
        );
        $this->set('attrs.data-options', $this->get('datas.options', []));

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
                'choices'   => $choices,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaults()
    {
        foreach($this->get('viewer', []) as $key => $value) :
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

        /** @var SelectJsChoices $choices */
        $choices_cb = wp_unslash(request()->post('_choices_cb'));
        $choices = new $choices_cb(ParamsBag::createFromAttrs(request()->post('args', [])));
        $choices->setField($this);

        $items = $choices->all();
        array_walk($items, [$choices, 'setItem']);

        wp_send_json($items);
    }
}