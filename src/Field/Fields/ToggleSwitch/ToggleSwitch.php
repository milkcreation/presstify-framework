<?php

namespace tiFy\Field\Fields\ToggleSwitch;

use tiFy\Contracts\Field\ToggleSwitch as ToggleSwitchContract;
use tiFy\Field\FieldController;

class ToggleSwitch extends FieldController implements ToggleSwitchContract
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
     *      @var string $label_on
     *      @var string $label_off
     *      @var bool|int|string $value_on
     *      @var bool|int|string $value_off
     * }
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'name'      => '',
        'value'     => 'on',
        'attrs'     => [],
        'viewer'    => [],
        'label_on'  => '',
        'label_off' => '',
        'value_on'  => 'on',
        'value_off' => 'off',
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
                    'FieldToggleSwitch',
                    asset()->url('field/toggle-switch/css/styles.css'),
                    [],
                    170724
                );
                wp_register_script(
                    'FieldToggleSwitch',
                    asset()->url('field/toggle-switch/js/scripts.js'),
                    ['jquery'],
                    170724
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'label_on'  => _x('Oui', 'FieldToggleSwitch', 'tify'),
            'label_off' => _x('Non', 'FieldToggleSwitch', 'tify'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('FieldToggleSwitch');
        wp_enqueue_script('FieldToggleSwitch');
    }
}