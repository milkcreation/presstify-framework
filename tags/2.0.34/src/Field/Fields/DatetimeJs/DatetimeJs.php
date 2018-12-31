<?php

namespace tiFy\Field\Fields\DatetimeJs;

use tiFy\Field\FieldController;

class DatetimeJs extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     *      @var string $format Format d'enregistrement de la valeur 'datetime': Y-m-d H:i:s|'date': Y-m-d|'time': H:i:s.
     *      @var bool $none_allowed Activation de permission d'utilisation de valeur de nulle liée au format de la valeur (ex: datetime 0000-00-00 00:00:00).
     *      @var array $fields {
     *          Liste des champs de saisie.
     *          Tableau indexés des champs de saisie (day|month|year|hour|minute|second) ou tableau associatif des attributs de champs.
     *          @see \tiFy\Field\Fields\Number\Number
     *          @see \tiFy\Field\Fields\Select\Select
     *      }
     * }
     */
    protected $attributes = [
        'before'       => '',
        'after'        => '',
        'name'         => '',
        'value'        => '',
        'attrs'        => [],
        'viewer'       => [],
        'format'       => 'datetime',
        'none_allowed' => false,
        'fields'       => [],
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                wp_register_style(
                    'FieldDatetimeJs',
                    assets()->url('field/datetime-js/css/styles.css'),
                    [],
                    171112
                );
                wp_register_script(
                    'FieldDatetimeJs',
                    assets()->url('field/datetime-js/js/scripts.js'),
                    ['jquery', 'moment'],
                    171112,
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
        wp_enqueue_style('FieldDatetimeJs');
        wp_enqueue_script('FieldDatetimeJs');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('fields', [])) :
            switch ($this->get('format')) :
                default :
                case 'datetime' :
                    $this->set('fields', ['year', 'month', 'day', 'hour', 'minute', 'second']);
                    break;
                case 'date' :
                    $this->set('fields', ['year', 'month', 'day']);
                    break;
                case 'time' :
                    $this->set('fields', ['year', 'month', 'day']);
                    break;
            endswitch;
        endif;

        $this->set('attrs.aria-control', 'datetime_js');
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        $date = new \DateTime($this->getValue());

        $Y = $date->format('Y');
        $m = $date->format('m');
        $d = $date->format('d');
        $H = $date->format('H');
        $i = $date->format('i');
        $s = $date->format('s');

        switch ($this->get('format')) :
            default :
            case 'datetime' :
                $value = "{$Y}-{$m}-{$d} {$H}:{$i}:{$s}";
                break;
            case 'date' :
                $value = "{$Y}-{$m}-{$d}";
                break;
            case 'time' :
                $value = "{$H}:{$i}:{$s}";
                break;
        endswitch;

        // Traitement des arguments des champs de saisie
        $year = '';
        $month = '';
        $day = '';
        $hour = '';
        $minute = '';
        $second = '';

        foreach ($this->get('fields') as $field_name => $field_attrs) :
            if (is_int($field_name)) :
                $field_name = (string)$field_attrs;
                $field_attrs = [];
            endif;

            switch ($field_name) :
                case 'year' :
                    $field_attrs = array_merge(
                        [
                            'attrs'      => [
                                'id'            => $this->getId() . "-handler-yyyy",
                                'class'         => 'tiFyField-DatetimeJsField tiFyField-DatetimeJsField--year',
                                'size'         => 4,
                                'maxlength'    => 4,
                                'min'          => 0,
                                'autocomplete' => 'off'
                            ],
                            'value'           => zeroise($Y, 4)
                        ],
                        $field_attrs
                    );

                    $year = field('number', $field_attrs);
                    break;

                case 'month' :
                    global $wp_locale;

                    $choices = [];
                    if ($this->get('none_allowed')) :
                        $choices[0] = __('Aucun', 'tify');
                    endif;
                    for ($n = 1; $n <= 12; $n++) :
                        $choices[zeroise($n, 2)] = $wp_locale->get_month_abbrev($wp_locale->get_month($n));
                    endfor;

                    $field_attrs = array_merge(
                        [
                            'attrs'      => [
                                'id'    => $this->getId() . "-handler-mm",
                                'class' => 'tiFyField-DatetimeJsField tiFyField-DatetimeJsField--month',
                                'autocomplete' => 'off'
                            ],
                            'choices'         => $choices,
                            'value'           => zeroise($m, 2)
                        ],
                        $field_attrs
                    );

                    $month = field('select', $field_attrs);
                    break;

                case 'day' :
                    $field_attrs = array_merge(
                        [
                            'attrs'      => [
                                'id'           => $this->getId() . "-handler-dd",
                                'class'        => 'tiFyField-DatetimeJsField tiFyField-DatetimeJsField--day',
                                'size'         => 2,
                                'maxlength'    => 2,
                                'min'          => $this->get('none_allowed') ? 0 : 1,
                                'max'          => 31,
                                'autocomplete' => 'off',
                            ],
                            'value'           => zeroise($d, 2)
                        ],
                        $field_attrs
                    );

                    $day = field('number', $field_attrs);
                    break;

                case 'hour' :
                    $field_attrs = array_merge(
                        [
                            'attrs'      => [
                                'id'           => $this->getId() . "-handler-hh",
                                'class'        => 'tiFyField-DatetimeJsField tiFyField-DatetimeJsField--hour',
                                'size'         => 2,
                                'maxlength'    => 2,
                                'min'          => 0,
                                'max'          => 23,
                                'autocomplete' => 'off'
                            ],
                            'value'           => zeroise($H, 2)
                        ],
                        $field_attrs
                    );

                    $hour = field('number', $field_attrs);
                    break;

                case 'minute' :
                    $field_attrs = array_merge(
                        [
                            'attrs'      => [
                                'id'           => $this->getId() . "-handler-ii",
                                'class'        => 'tiFyField-DatetimeJsField tiFyField-DatetimeJsField--minute',
                                'size'         => 2,
                                'maxlength'    => 2,
                                'min'          => 0,
                                'max'          => 59,
                                'autocomplete' => 'off'
                            ],
                            'value'           => zeroise($i, 2)
                        ],
                        $field_attrs
                    );

                    $minute = field('number', $field_attrs);
                    break;

                case 'second' :
                    $field_attrs = array_merge(
                        [
                            'attrs'      => [
                                'id'           => $this->getId() . "-handler-ss",
                                'class'        => 'tiFyField-DatetimeJsField tiFyField-DatetimeJsField--second',
                                'size'         => 2,
                                'maxlength'    => 2,
                                'min'          => 0,
                                'max'          => 59,
                                'autocomplete' => 'off'
                            ],
                            'value'           => zeroise($s, 2)
                        ],
                        $field_attrs
                    );

                    $second = field('number', $field_attrs);
                    break;
            endswitch;
        endforeach;

        ob_start();
?><?php $this->before(); ?>
    <div <?php echo $this->attrs(); ?>>
        <?php printf('%3$s %2$s %1$s %4$s %5$s %6$s', $year, $month, $day, $hour, $minute, $second); ?>

        <?php
            echo field(
                'hidden',
                [
                    'attrs' => [
                        'id'           => 'tiFyField-DatetimeJsInput--' . $this->getIndex(),
                        'class'        => 'tiFyField-DatetimeJsField tiFyField-DatetimeJsField--value',
                        'name'         => $this->getName(),
                        'value'        => $value
                    ]
                ]
            );
        ?>
    </div>
<?php $this->after(); ?><?php

        return ob_get_clean();
    }
}