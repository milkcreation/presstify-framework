<?php

namespace tiFy\Partial\Table;

use tiFy\Partial\AbstractPartialItem;

class Table extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le controleur d'affichage.
     *      @var string $after Contenu placé après le controleur d'affichage.
     *      @var bool $header Activation de l'entête de table.
     *      @var bool $footer Activation du pied de table.
     *      @var string[] $columns Intitulé des colonnes.
     *      @var array[] $datas Données de la table.
     *      @var string $none Intitulé de la table lorsque la table ne contient aucune donnée.
     * }
     */
    protected $attributes = [
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
        app()->appAddAction(
            'init',
            function () {
                \wp_register_style(
                    'PartialTable',
                    \assets()->url('/partial/table/css/styles.css'),
                    [],
                    160714
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
        \wp_enqueue_style('PartialTable');
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    public function parse($attrs = [])
    {
        $this->set('none', __('Aucun élément à afficher dans le tableau', 'tify'));

        parent::parse($attrs);

        $this->set('count', count($this->get('columns', [])));
    }
}