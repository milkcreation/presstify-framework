<?php

namespace tiFy\Components\Partial\Table;

use tiFy\Partial\AbstractPartialController;

class Table extends AbstractPartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *
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
        $this->attributes['none'] = __('Aucun élément à afficher dans le tableau', 'tify');

        parent::parse($attrs);
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        /**
         * @var array $columns
         * @var array $datas
         */
        extract($this->attributes);

        $count = count($columns);
        $num = 0;

        $header = $header
            ? $this->appTemplateRender(
                    'header',
                    compact('datas', 'columns', 'count', 'num')
                )
            : '';

        $footer = $footer
            ? $this->appTemplateRender(
                'footer',
                compact('datas', 'columns', 'count', 'num')
            )
            : '';

        $body = $this->appTemplateRender(
            'body',
            compact('datas', 'columns', 'count', 'num', 'none')
        );

        return $this->appTemplateRender(
                'table',
                compact('header', 'footer', 'body', 'datas', 'columns', 'count', 'num', 'none')
        );
    }
}