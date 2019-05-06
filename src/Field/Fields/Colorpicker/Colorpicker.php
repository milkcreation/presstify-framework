<?php

namespace tiFy\Field\Fields\Colorpicker;

use tiFy\Contracts\Field\Colorpicker as ColorpickerContract;
use tiFy\Field\FieldController;

class Colorpicker extends FieldController implements ColorpickerContract
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
     *      @var array $options {
     *          Liste des options du contrôleur ajax.
     *          @see https://bgrins.github.io/spectrum/
     *      }
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'name'    => '',
        'value'   => '',
        'attrs'   => [],
        'viewer'  => [],
        'options' => [],
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
                    'FieldColorpicker',
                    asset()->url('field/colorpicker/css/styles.css'),
                    ['spectrum'],
                    180725
                );

                $deps = ['jquery', 'spectrum'];
                if (wp_script_is('spectrum-i10n', 'registered')) :
                    $deps[] = 'spectrum-i10n';
                endif;

                wp_register_script(
                    'FieldColorpicker',
                    asset()->url('field/colorpicker/js/scripts.js'),
                    $deps,
                    180725,
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
        wp_enqueue_style('FieldColorpicker');
        wp_enqueue_script('FieldColorpicker');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $options = array_merge(
            [
                'preferredFormat' => 'hex',
                'showInput' => true
            ],
            $this->get('options', [])
        );

        $this->set('attrs.data-options', $options);
    }
}