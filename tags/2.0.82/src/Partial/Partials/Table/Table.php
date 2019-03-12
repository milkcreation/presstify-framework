<?php

namespace tiFy\Partial\Partials\Table;

use tiFy\Contracts\Partial\Table as TableContract;
use tiFy\Partial\PartialController;

class Table extends PartialController implements  TableContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var bool $header Activation de l'entête de table.
     *      @var bool $footer Activation du pied de table.
     *      @var string[] $columns Intitulé des colonnes.
     *      @var array[] $datas Données de la table.
     *      @var string $none Intitulé de la table lorsque la table ne contient aucune donnée.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'viewer'  => [],
        'header'  => true,
        'footer'  => true,
        'columns' => [
            'Lorem', 'Ipsum'
        ],
        'datas'   => [
            [
                'lorem dolor', 'ipsum dolor'
            ],
            [
                'lorem amet', 'ipsum amet'
            ]
        ],
        'none'    => ''
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
                    'PartialTable',
                    assets()->url('partial/table/css/styles.css'),
                    [],
                    160714
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
            'none' => __('Aucun élément à afficher dans le tableau', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialTable');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('count', count($this->get('columns', [])));
    }
}