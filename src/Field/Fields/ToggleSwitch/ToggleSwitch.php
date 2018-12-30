<?php

namespace tiFy\Field\Fields\ToggleSwitch;

use tiFy\Field\FieldController;

class ToggleSwitch extends FieldController
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
     *      @var string $label_on
     *      @var string $label_off
     *      @var bool|int|string $value_on
     *      @var bool|int|string $value_off
     * }
     */
    protected $attributes = [
        'before'          => '',
        'after'           => '',
        'attrs'           => [],
        'name'            => '',
        'value'           => 'on',
        'label_on'        => '',
        'label_off'       => '',
        'value_on'        => 'on',
        'value_off'       => 'off',
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
                    assets()->url('field/toggle-switch/css/styles.css'),
                    [],
                    170724
                );
                wp_register_script(
                    'FieldToggleSwitch',
                    assets()->url('field/toggle-switch/js/scripts.js'),
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
            'label_on'  => _x('Oui', 'tiFyFieldToggleSwitch', 'tify'),
            'label_off' => _x('Non', 'tiFyFieldToggleSwitch', 'tify'),
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