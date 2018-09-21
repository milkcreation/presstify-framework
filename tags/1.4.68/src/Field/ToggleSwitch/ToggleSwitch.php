<?php

/**
 * @name ToggleSwitch
 * @desc Bouton de bascule on/off
 * @see http://php.quicoto.com/toggle-switches-using-input-radio-and-css3/
 * @see https://github.com/ghinda/css-toggle-switch
 * @see http://bashooka.com/coding/pure-css-toggle-switches/
 * @see https://proto.io/freebies/onoff/
 * @package presstiFy
 * @namespace tiFy\Field\ToggleSwitch
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Field\ToggleSwitch;

use tiFy\Field\AbstractFieldItem;
use tiFy\Field\Field;

class ToggleSwitch extends AbstractFieldItem
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
                \wp_register_style(
                    'FieldToggleSwitch',
                    assets()->url('/field/toggle-switch/css/styles.css'),
                    [],
                    170724
                );
                \wp_register_script(
                    'FieldToggleSwitch',
                    assets()->url('/field/toggle-switch/js/scripts.js'),
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
        \wp_enqueue_style('FieldToggleSwitch');
        \wp_enqueue_script('FieldToggleSwitch');
    }
}