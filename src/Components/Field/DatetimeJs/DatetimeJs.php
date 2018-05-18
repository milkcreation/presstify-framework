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

use tiFy\Field\AbstractFieldController;
use tiFy\Field\Field;

class DatetimeJs extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $container_id Id HTML du conteneur du champ.
     *      @var string $container_class Classe HTML du conteneur du champ.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
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
        'container_id'    => '',
        'container_class' => '',
        'name'            => '',
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
            $this->appAsset('/Field/DatetimeJs/css/styles.css'),
            [],
            171112
        );
        \wp_register_script(
            'tiFyFieldDatetimeJs',
            $this->appAsset('/Field/DatetimeJs/js/scripts.js'),
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
     * Traitement des attributs de configuration
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        $this->attributes['container_id'] = 'tiFyField-datetimeJs--' . $this->getIndex();

        parent::parse($attrs);

        if (!isset($this->attributes['container_class'])) :
            $this->attributes['container_class'] = 'tiFyField-datetimeJs ' . $this->attributes['container_class'];
        else :
            $this->attributes['container_class'] = 'tiFyField-datetimeJs';
        endif;

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
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
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
                            'class'         => 'tiFyField-datetimeJsField tiFyField-datetimeJsField--year',
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
                            'class' => 'tiFyField-datetimeJsField tiFyField-datetimeJsField--month',
                            'autocomplete' => 'off'
                        ],
                        'selected'        => $m,
                        'options'         => $field_options,
                        'value'           => zeroise($m, 4)
                    ];
                    $field_attrs = \wp_parse_args($field_attrs, $defaults);

                    $month = Field::Select($field_attrs);
                    break;

                case 'day' :
                    $defaults = [
                        'attrs'      => [
                            'id'           => $this->getId() . "-handler-dd",
                            'class'        => 'tiFyField-datetimeJsField tiFyField-datetimeJsField--day',
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
                            'class'        => 'tiFyField-datetimeJsField tiFyField-datetimeJsField--hour',
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
                            'class'        => 'tiFyField-datetimeJsField tiFyField-datetimeJsField--minute',
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
                            'class'        => 'tiFyField-datetimeJsField tiFyField-datetimeJsField--second',
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
    <div
        id="<?php echo $this->get('container_id'); ?>"
        class="<?php echo $this->get('container_class'); ?>"
    >
        <?php printf('%3$s %2$s %1$s %4$s %5$s %6$s', $year, $month, $day, $hour, $minute, $second); ?>

        <?php
            echo Field::Hidden(
                [
                    'attrs' => [
                        'id'           => 'tiFyField-datetimeJsInput--' . $this->getIndex(),
                        'class'        => 'tiFyField-datetimeJsField tiFyField-datetimeJsField--value',
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