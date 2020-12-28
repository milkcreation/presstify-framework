<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\Drivers\SelectJs\SelectJsChoices;
use tiFy\Field\Drivers\SelectJs\SelectJsChoicesInterface;
use tiFy\Field\FieldDriver;
use tiFy\Support\Arr;
use tiFy\Support\ParamsBag;

class SelectJsDriver extends FieldDriver implements SelectJsDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                /**
                 * @var bool|array $ajax Activation ou liste des attributs de requête de récupération Ajax des élèments.
                 */
                'ajax'      => false,
                /**
                 *
                 */
                'classes'   => [],
                /**
                 * @var array $choices Liste des choix de selection disponibles. La récupération Ajax doit être inactive.
                 */
                'choices'   => [],
                /**
                 * @todo bool $autocomplete Activation le champs de selection par autocomplétion.
                 */
                //@todo 'autocomplete' => false,
                /**
                 * @var bool $disabled Activation/Désactivation du controleur de champ.
                 */
                //@todo 'disabled'     => false,
                /**
                 * @var int $max Nombre d'éléments maximum (multiple uniquement). défaut -1 pas de limite.
                 */
                'max'       => -1,
                /**
                 * @var bool $multiple Activation la selection multiple d'éléments.
                 */
                'multiple'  => false,
                /**
                 * @var array $picker {
                 * Liste des attributs de configuration de l'interface du selecteur d'éléments.
                 * @todo array $attrs Liste des attributs HTML.
                 * - @var string $appendTo Selecteur jQuery de positionnement dans le DOM. défaut body.
                 * - @var string $placement Comportement de la liste déroulante. top|bottom|clever. défaut clever (adaptatif).
                 * - @var array $delta {
                 * - Liste des valeurs d'ajustements de positionnement. Exprimée en px.
                 * -- @var int $top
                 * -- @var int $left
                 * -- @var int $width
                 * - }
                 * - @var bool $filter Activation du champ de filtrage des éléments.
                 * - @var string $loader Rendu de l'indicateur de préchargement.
                 * - @var string $more Rendu de '+'.
                 * }
                 */
                'picker'    => [],
                /**
                 * @var bool $removable Activation de la suppression des éléments depuis la liste des éléments sélectionnés. (multiple uniquement).
                 */
                'removable' => true,
                /**
                 * @var bool|array $sortable Activation|Liste des options du contrôleur ajax d'ordonnancement. (multiple uniquement).
                 * @see https://jqueryui.com/sortable/
                 */
                'sortable'  => false,
                /**
                 * @var bool $trigger Activation de l'affichage de l'interface d'ouverture et de fermeture du selecteur.
                 */
                'trigger'   => true,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        $value = $this->get('value', null);

        if (is_null($value)) {
            return null;
        }

        $value = is_string($value)
            ? array_map('trim', explode(',', $value))
            : (array)$value;
        $value = $this->get('duplicate') ? $value : array_unique($value);
        $value = $this->get('multiple') ? $value : [reset($value)];

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('attrs.class', trim(sprintf($this->get('attrs.class', '%s'), ' FieldSelectJs')));
        if (!$this->has('attrs.data-control')) {
            $this->set('attrs.data-control', 'select-js');
        }
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
        foreach ($classes as $key => &$class) {
            $class = sprintf($this->get("classes.{$key}", '%s'), $class);
        }
        $this->set('classes', $classes);

        $choices = $this->get('choices', []);
        if (!$choices instanceof SelectJsChoicesInterface) {
            if ($args = $this->get('ajax.args', [])) {
                $choices = ParamsBag::createFromAttrs($args);
            }

            $choices = new SelectJsChoices($choices, $this->getValue());
        }
        $this->set('choices', $choices->setField($this));

        $this->set(
            'datas.options',
            [
                'ajax'         => ($this->get('ajax') === false)
                    ? false
                    : array_merge(
                        [
                            'url'    => $this->getXhrUrl(),
                            'data'   => [
                                '_id'         => $this->getId(),
                                '_viewer'     => $this->get('viewer', []),
                                '_choices_cb' => class_info($choices)->getName(),
                                'args'        => [],
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
                                    'class' => 'FieldSelectJs-pickerSpinkit',
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
                    'max_attempt' => __('Le nombre maximum de valeurs autorisées est atteint.', 'tify'),
                ],
            ]
        );
        $this->set('attrs.data-options', $this->get('datas.options', []));

        $this->set(
            'handler',
            array_merge(
                $this->get('handler') ?: [],
                [
                    'name'      => $this->getName(),
                    'disabled'  => $this->get('disabled'),
                    'removable' => $this->get('removable'),
                    'multiple'  => $this->get('multiple'),
                    'choices'   => $choices,
                ]
            )
        );
        $this->set('handler.attrs.data-control', 'select-js.handler');

        return parent::render();
    }

    /**
     * Génération de réponse HTTP via un requête XHR.
     *
     * @param array ...$args Liste de variables passées en arguments à la requête.
     *
     * @return array
     */
    public function xhrResponse(...$args): array
    {
        $this->set('viewer', request()->input('_viewer', []));

        /** @var SelectJsChoices $choices */
        $choices_cb = Arr::stripslashes(request()->post('_choices_cb'));
        $choices = new $choices_cb(ParamsBag::createFromAttrs(request()->post('args', [])));
        $choices->setField($this);

        $items = $choices->all();
        array_walk($items, [$choices, 'setItem']);

        return [
            'success' => true,
            'data'    => $items,
        ];
    }
}