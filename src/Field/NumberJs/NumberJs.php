<?php

namespace tiFy\Field\NumberJs;

use tiFy\Field\FieldController;

class NumberJs extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $container Liste des attribut de configuration du conteneur de champ
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var int $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var array $data-options {
     *          Liste des options du contrôleur ajax.
     *          @see http://api.jqueryui.com/spinner/
     *      }
     * }
     */
    protected $attributes = [
        'before'          => '',
        'after'           => '',
        'container'       => '',
        'attrs'           => [],
        'name'            => '',
        'value'           => 0,
        'data-options'    => []
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
                    'FieldNumberJs',
                    assets()->url('field/number-js/css/styles.css'),
                    ['dashicons'],
                    171019
                );
                \wp_register_script(
                    'FieldNumberJs',
                    assets()->url('field/number-js/js/scripts.css'),
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
        \wp_enqueue_style('FieldNumberJs');
        \wp_enqueue_script('FieldNumberJs');
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
                $this->get('data-options', [])
            )
        );
        $this->set('attrs.aria-control', 'number_js');
    }
}