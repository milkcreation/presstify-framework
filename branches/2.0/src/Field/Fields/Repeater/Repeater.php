<?php

namespace tiFy\Field\Fields\Repeater;

use tiFy\Contracts\Field\Repeater as RepeaterContract;
use tiFy\Field\FieldController;

class Repeater extends FieldController implements RepeaterContract
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
     *      @var array $ajax Liste des arguments de requête de récupération des éléments via Ajax.
     *      @var array $button Liste des attributs de configuration du bouton d'ajout d'un élément.
     *      @var int $max Nombre maximum de valeur pouvant être ajoutées. -1 par défaut, pas de limite.
     *      @var boolean $removable Activation du déclencheur de suppression des éléments.
     *      @var bool|array $sortable Activation de l'ordonnacemment des éléments|Liste des attributs de configuration.
     *                                @see http://api.jqueryui.com/sortable/
     *      @var array $args Arguments complémentaires porté par la requête Ajax.
     * }
     */
    protected $attributes = [
        'before'      => '',
        'after'       => '',
        'name'        => '',
        'value'       => '',
        'attrs'       => [],
        'viewer'      => [],
        'ajax'        => [],
        'button'      => [],
        'max'         => -1,
        'removable'   => true,
        'sortable'    => true,
        'args'        => [],
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

                wp_register_style(
                    'FieldRepeater',
                    assets()->url('field/repeater/css/styles.css'),
                    [is_admin() ? 'tiFyAdmin' : ''],
                    170421
                );

                wp_register_script(
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
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('FieldRepeater');
        wp_enqueue_script('FieldRepeater');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->set('button.content', __('Ajouter un élément', 'tify'));

        parent::parse($attrs);

        $this->set('attrs.class', trim(sprintf($this->get('attrs.class', '%s'), ' FieldRepeater')));

        $this->set('attrs.data-id', $this->getId());

        $this->set('attrs.data-control', 'repeater');

        if (!$this->get('button.tag')) :
            $this->set('button.tag', 'a');
        endif;
        if(($this->get('button.tag') === 'a') && !$this->get('button.attrs.href')) :
            $this->set('button.attrs.href', "#{$this->get('attrs.id')}");
        endif;
        if (! $this->get('button.attrs.class')) :
            $this->set('button.attrs.class', 'FieldRepeater-buttonAdd' . (is_admin() ? ' button-secondary' : ''));
        endif;
        $this->set('button.attrs.data-control', 'repeater.trigger');

        if ($sortable = $this->get('sortable')) :
            if (!is_array($sortable)) :
                $sortable = [];
            endif;
            $this->set(
                'sortable',
                array_merge(
                    [
                        'placeholder' => 'FieldRepeater-itemPlaceholder',
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
                'ajax'      => array_merge(
                    [
                        'url'    => admin_url('admin-ajax.php', 'relative'),
                        'data'   => [
                            'action'      => 'field_repeater',
                            '_ajax_nonce' => wp_create_nonce('FieldRepeater' . $this->getId()),
                            '_id'         => $this->getId(),
                            '_viewer'     => $this->get('viewer'),
                            'args'        => $this->get('args', []),
                            'max'         => $this->get('max'),
                            'name'        => $this->getName(),
                            'order'       => $this->get('order'),
                        ],
                        'method' => 'post',
                    ],
                    $this->get('ajax', [])
                ),
                'removable' => $this->get('removable'),
                'sortable'  => $this->get('sortable'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaults()
    {
        foreach($this->get('viewer', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
    }

    /**
     * Génération d'un indice aléatoire.
     *
     * @param int $index Clé d'indice de la valeur de soumission.
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
     * @return void
     */
    public function wp_ajax()
    {
        $params = params(request()->request->all());

        check_ajax_referer('FieldRepeater' . $params->get('_id'));

        if (($params->get('max') > 0) && ($params->get('index') >= $params->get('max'))) :
            wp_send_json_error(__('Nombre de valeur maximum atteinte', 'tify'));
        else :
            $this->set('name', $params->get('name'));
            $this->set('viewer', $params->get('_viewer', []));

            wp_send_json_success(
                (string)$this->viewer(
                    'item-wrap',
                    array_merge(
                        $params->all(),
                        ['value' => '']
                    )
                )
            );
        endif;
    }
}