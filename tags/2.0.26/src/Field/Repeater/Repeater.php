<?php

namespace tiFy\Field\Repeater;

use tiFy\Field\FieldController;

class Repeater extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $name Clé d'indice de la valeur à enregistrer.
     *      @var array $value Liste de valeurs existantes.
     *      @var string $ajax_action Action Ajax lancée pour récupérer le formulaire d'un élément.
     *      @var string $ajax_nonce Agent de sécurisation de la requête de récupération Ajax.
     *      @var array $attrs Liste des attributs HTML de la balise HTML.
     *      @var array $button Liste des attributs de configuration du bouton d'ajout d'un élément.
     *      @var int $max Nombre maximum de valeur pouvant être ajoutées. -1 par défaut, pas de limite.
     *      @var bool|array $sortable Activation de l'ordonnacemment des éléments|Liste des attributs de configuration. @see http://api.jqueryui.com/sortable/
     *      @var array $args Arguments complémentaires porté par la requête Ajax.
     *      @var array $templates Attributs de configuration des templates.
     * }
     */
    protected $attributes = [
        'name'             => '',
        'value'            => [],
        'ajax_action'      => 'field_repeater',
        'ajax_nonce'       => '',
        'attrs'            => [],
        'button'           => [],
        'max'              => -1,
        'sortable'         => true,
        'args'             => [],
        'viewer'           => []
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                add_action(
                    'wp_ajax_field_repeater',
                    [$this, 'wp_ajax']
                );
                add_action(
                    'wp_ajax_nopriv_field_repeater',
                    [$this, 'wp_ajax']
                );

                \wp_register_style(
                    'FieldRepeater',
                    assets()->url('field/repeater/css/styles.css'),
                    [is_admin() ? 'tiFyAdmin' : ''],
                    170421
                );
                \wp_register_script(
                    'FieldRepeater',
                    assets()->url('field/repeater/js/scripts.js'),
                    ['jquery', 'jquery-ui-sortable'],
                    170421,
                    true
                );
            }
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('FieldRepeater');
        wp_enqueue_script('FieldRepeater');
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'ajax_nonce' => wp_create_nonce('FieldRepeater')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->set('button.content', __('Ajouter un élément', 'tify'));

        parent::parse($attrs);

        $this->set('attrs.aria-control', 'repeater');
        $this->set('attrs.aria-id', $this->getId());
        $this->set('attrs.aria-sortable', $this->get('sortable') ? 'true' : 'false');

        if (!$this->get('button.tag')) :
            $this->set('button.tag', 'a');
        endif;
        if(($this->get('button.tag') === 'a') && !$this->get('button.attrs.href')) :
            $this->set('button.attrs.href', "#{$this->get('attrs.id')}");
        endif;
        if (! $this->get('button.attrs.class')) :
            $this->set('button.attrs.class', 'tiFyPartial-RepeaterAddItem' . (is_admin() ? ' button-secondary' : ''));
        endif;
        $this->set('button.attrs.aria-control', 'add');

        if ($sortable = $this->get('sortable')) :
            if (!is_array($sortable)) :
                $sortable = [];
            endif;
            $this->set(
                'sortable',
                array_merge(
                    [
                        'placeholder' => 'tiFyField-RepeaterItemPlaceholder',
                        'axis'        => 'y'
                    ],
                    $sortable
                )
            );

            $this->set('order', '__order_' . $this->getName());
        endif;

        $this->set(
            'attrs.data-options',
            [
                'ajax_action' => $this->get('ajax_action'),
                'ajax_nonce'  => $this->get('ajax_nonce'),
                'name'        => $this->getName(),
                'max'         => $this->get('max'),
                'order'       => $this->get('order'),
                'sortable'    => $this->get('sortable', []),
                'args'        => $this->get('args'),
                'viewer'      => $this->get('viewer')
            ]
        );
    }

    /**
     * Génération d'un indice aléatoire
     *
     * @param
     *
     * @return string
     */
    public function parseIndex($index = 0)
    {
        if (!is_numeric($index)) :
            return $index;
        endif;

        return uniqid();
    }

    /**
     * Récupération des champs via Ajax.
     *
     * @return string
     */
    public function wp_ajax()
    {
        check_ajax_referer('FieldRepeater');

        $options = wp_unslash(request()->post('options'));
        $index = absint(request()->post('index'));

        if (($options['max'] > 0) && ($index >= $options['max'])) :
            wp_send_json_error(__('Nombre de valeur maximum atteinte', 'tify'));
        else :
            foreach($options as $key => $value) :
                $this->set($key, $value);
            endforeach;

            wp_send_json_success(
                $this->viewer(
                    'item-wrap',
                    array_merge(
                        $this->all(),
                        ['index' => $index, 'value' => '']
                    )
                )->render()
            );
        endif;
    }
}