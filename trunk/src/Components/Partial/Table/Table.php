<?php

namespace tiFy\Components\Partial\Table;

use tiFy\Partial\AbstractPartialController;

class Table extends AbstractPartialController
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
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            'tiFyPartialTable',
            $this->appAsset('/Partial/Table/css/styles.css'),
            [],
            160714
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('tiFyPartialTable');
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