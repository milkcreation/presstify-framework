<?php

namespace tiFy\Field\Fields\NumberJs;

use tiFy\Contracts\Field\NumberJs as NumberJsContract;
use tiFy\Field\FieldController;

class NumberJs extends FieldController implements NumberJsContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var int $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     *      @var string $container Liste des attribut de configuration du conteneur de champ
     *      @var array $options {
     *          Liste des options du contrôleur ajax.
     *          @see http://api.jqueryui.com/spinner/
     *      }
     * }
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'name'      => '',
        'value'     => 0,
        'attrs'     => [],
        'viewer'    => [],
        'container' => [],
        'options'   => [],
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
                    'FieldNumberJs',
                    asset()->url('field/number-js/css/styles.css'),
                    ['dashicons'],
                    171019
                );
                wp_register_script(
                    'FieldNumberJs',
                    asset()->url('field/number-js/js/scripts.css'),
                    ['jquery-ui-spinner'],
                    171019,
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
        wp_enqueue_style('FieldNumberJs');
        wp_enqueue_script('FieldNumberJs');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->set('container.attrs.id', 'tiFyField-NumberJsContainer--' . $this->getIndex());

        parent::parse($attrs);

        if ($container_class = $this->get('container.attrs.class')) :
            $this->set('container.attrs.class', "tiFyField-NumberJsContainer {$container_class}");
        else :
            $this->set('container.attrs.class', 'tiFyField-NumberJsContainer');
        endif;

        if (!$this->has('attrs.id')) :
            $this->set('attrs.id', 'tiFyField-NumberJs--' . $this->getIndex());
        endif;
        $this->set('attrs.type', 'text');
        $this->set(
            'attrs.data-options',
            array_merge(
                [
                    'icons' => [
                        'down' => 'dashicons dashicons-arrow-down-alt2',
                        'up'   => 'dashicons dashicons-arrow-up-alt2',
                    ]
                ],
                $this->get('options', [])
            )
        );
        $this->set('attrs.aria-control', 'number_js');
    }
}