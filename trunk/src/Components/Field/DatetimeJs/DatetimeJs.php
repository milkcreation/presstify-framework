<?php

/**
 * @name DatetimeJs
 * @desc Selecteur de date et heure JS
 * @package presstiFy
 * @namespace tiFy\Components\Field\DatetimeJs
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\DatetimeJs;

use tiFy\Field\AbstractFieldItemController;
use tiFy\Field\Field;

class DatetimeJs extends AbstractFieldItemController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var string $format Format d'enregistrement de la valeur 'datetime': Y-m-d H:i:s|'date': Y-m-d|'time': H:i:s.
     *      @var bool $none_allowed Activation de permission d'utilisation de valeur de nulle liée au format de la valeur (ex: datetime 0000-00-00 00:00:00).
     *      @var array $fields {
     *          Liste des champs de saisie.
     *          Tableau indexés des champs de saisie (day|month|year|hour|minute|second) ou tableau associatif des attributs de champs.
     *          @see \tiFy\Components\Field\Number\Number|\tiFy\Components\Field\Select\Select
     *      }
     * }
     */
    protected $attributes = [
        'before'          => '',
        'after'           => '',
        'name'            => '',
        'attrs'           => [],
        'value'           => '',
        'format'          => 'datetime',
        'none_allowed'    => false,
        'fields'          => [],
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyFieldDatetimeJs',
            $this->appAssetUrl('/Field/DatetimeJs/css/styles.css'),
            [],
            171112
        );
        \wp_register_script(
            'tiFyFieldDatetimeJs',
            $this->appAssetUrl('/Field/DatetimeJs/js/scripts.js'),
            ['jquery', 'moment'],
            171112,
            true
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyFieldDatetimeJs');
        \wp_enqueue_script('tiFyFieldDatetimeJs');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->attributes['fields']) :
            switch ($this->attributes['format']) :
                default :
                case 'datetime' :
                    $this->attributes['fields'] = ['year', 'month', 'day', 'hour', 'minute', 'second'];
                    break;
                case 'date' :
                    $this->attributes['fields'] = ['year', 'month', 'day'];
                    break;
                case 'time' :
                    $this->attributes['fields'] = ['year', 'month', 'day'];
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
        // Traitement de la valeur
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
                    $defaults = [
                        'attrs'      => [
                            'id'            => $this->getId() . "-handler-yyyy",
                            'class'         => 'tiFyField-DatetimeJsField tiFyField-DatetimeJsField--year',
                            'size'         => 4,
                            'maxlength'    => 4,
                            'min'          => 0,
                            'autocomplete' => 'off'
                        ],
                        'value'           => zeroise($Y, 4)
                    ];
                    $field_attrs = \wp_parse_args($field_attrs, $defaults);

                    $year = Field::Number($field_attrs);
                    break;

                case 'month' :
                    global $wp_locale;

                    $field_options = [];
                    if ($this->get('none_allowed')) :
                        $field_options[0] = __('Aucun', 'tify');
                    endif;
                    for ($n = 1; $n <= 12; $n++) :
                        $field_options[zeroise($n, 2)] = $wp_locale->get_month_abbrev($wp_locale->get_month($n));
                    endfor;
                    $defaults = [
                        'attrs'      => [
                            'id'    => $this->getId() . "-handler-mm",
                            'class' => 'tiFyField-DatetimeJsField tiFyField-DatetimeJsField--month',
                            'autocomplete' => 'off'
                        ],
                        'options'         => $field_options,
                        'value'           => zeroise($m, 2)
                    ];
                    $field_attrs = \wp_parse_args($field_attrs, $defaults);

                    $month = Field::Select($field_attrs);
                    break;

                case 'day' :
                    $defaults = [
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
                    ];
                    $field_attrs = \wp_parse_args($field_attrs, $defaults);

                    $day = Field::Number($field_attrs);
                    break;

                case 'hour' :
                    $defaults = [
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
                    ];
                    $field_attrs = \wp_parse_args($field_attrs, $defaults);

                    $hour = Field::Number($field_attrs);
                    break;

                case 'minute' :
                    $defaults = [
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
                    ];
                    $field_attrs = \wp_parse_args($field_attrs, $defaults);

                    $minute = Field::Number($field_attrs);
                    break;

                case 'second' :
                    $defaults = [
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
                    ];
                    $field_attrs = \wp_parse_args($field_attrs, $defaults);

                    $second = Field::Number($field_attrs);
                    break;
            endswitch;
        endforeach;

        ob_start();
?><?php $this->before(); ?>
    <div <?php echo $this->attrs(); ?>>
        <?php printf('%3$s %2$s %1$s %4$s %5$s %6$s', $year, $month, $day, $hour, $minute, $second); ?>

        <?php
            echo Field::Hidden(
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