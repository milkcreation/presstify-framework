<?php

/**
 * @name ToggleSwitch
 * @desc Bouton de bascule on/off
 * @see http://php.quicoto.com/toggle-switches-using-input-radio-and-css3/
 * @see https://github.com/ghinda/css-toggle-switch
 * @see http://bashooka.com/coding/pure-css-toggle-switches/
 * @see https://proto.io/freebies/onoff/
 * @package presstiFy
 * @namespace tiFy\Components\Field\ToggleSwitch
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\ToggleSwitch;

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
        'container_id'    => '',
        'container_class' => '',
        'name'            => '',
        'value'           => 'on',
        'label_on'        => '',
        'label_off'       => '',
        'value_on'        => 'on',
        'value_off'       => 'off',
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyFieldToggleSwitch',
            $this->appAssetUrl('/Field/ToggleSwitch/css/styles.css'),
            [],
            170724
        );
        \wp_register_script(
            'tiFyFieldToggleSwitch',
            $this->appAssetUrl('/Field/ToggleSwitch/js/scripts.js'),
            ['jquery'],
            170724
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyFieldToggleSwitch');
        \wp_enqueue_script('tiFyFieldToggleSwitch');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->set('container_id', 'tiFyField-ToggleSwitch--' . $this->getIndex());
        $this->set('label_on', _x('Oui', 'tiFyFieldToggleSwitch', 'tify'));
        $this->set('label_off', _x('Non', 'tiFyFieldToggleSwitch', 'tify'));

        parent::parse($attrs);

        if (!$class = $this->get('container_class')) :
            $this->set('container_class', 'tiFyField-ToggleSwitch');
        else :
            $this->set('container_class', 'tiFyField-ToggleSwitch ' . $class);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        ob_start();
        ?><?php $this->before(); ?>
        <div id="<?php echo $this->get('container_id'); ?>" class="<?php echo $this->get('container_class'); ?>">
            <div class="tiFyField-ToggleSwitchWrapper">
                <?php
                echo Field::Radio(
                    [
                        'after'   => (string)Field::Label([
                            'content' => $this->get('label_on'),
                            'attrs'   => [
                                'for'   => $this->getId() . '--on',
                                'class' => 'tiFyField-ToggleSwitchLabel tiFyField-ToggleSwitchLabel--on'
                            ]
                        ]),
                        'attrs'   => [
                            'id'           => $this->getId() . '--on',
                            'class'        => 'tiFyField-ToggleSwitchRadio tiFyField-ToggleSwitchRadio--on',
                            'autocomplete' => 'off'
                        ],
                        'name'    => $this->getName(),
                        'value'   => $this->get('value_on'),
                        'checked' => $this->getValue()
                    ],
                    true
                );
                ?>
                <?php
                echo Field::Radio(
                    [
                        'after'   => (string)Field::Label(
                            [
                                'content' => $this->get('label_off'),
                                'attrs'   => [
                                    'for'   => $this->getId() . '--off',
                                    'class' => 'tiFyField-ToggleSwitchLabel tiFyField-ToggleSwitchLabel--off'
                                ]
                            ]
                        ),
                        'attrs'   => [
                            'id'           => $this->getId() . '--off',
                            'class'        => 'tiFyField-ToggleSwitchRadio tiFyField-ToggleSwitchRadio--off',
                            'autocomplete' => 'off'
                        ],
                        'name'    => $this->getName(),
                        'value'   => $this->get('value_off'),
                        'checked' => $this->getValue()
                    ],
                    true
                );
                ?>
                <span class="tiFyField-ToggleSwitchHandler"></span>
            </div>
        </div>
        <?php $this->after(); ?>
        <?php

        return ob_get_clean();
    }
}