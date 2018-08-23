<?php

/**
 * @name Table
 * @desc Controleur d'affichage de Tableau HTML Responsive
 * @package presstiFy
 * @namespace tiFy\Core\Control\Table
 * @version 1.1
 * @subpackage Core
 * @since 1.2.502
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Core\Control\Table;

/**
 * @Overrideable \App\Core\Control\Table\Table
 *
 * <?php
 * namespace \App\Core\Control\Table
 *
 * class Table extends \tiFy\Core\Control\Table\Table
 * {
 *
 * }
 */

class Table extends \tiFy\Core\Control\Factory
{
    /**
     * Initialisation globale.
     *
     * @return void
     */
    protected function init()
    {
        \wp_register_style(
            'tify_control-table',
            $this->appAbsUrl() . '/assets/Table/css/styles.css',
            [],
            160714
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    protected function enqueue_scripts()
    {
        \wp_enqueue_style('tify_control-table');
    }

    /**
     * Affichage
     *
     * @param array $attrs Liste des attributs de configuration
     *
     * @return string
     */
    protected function display($attrs = [])
    {
        // Traitement des attributs de configuration
        $defaults = [
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
            'none'    => __('Aucun élément à afficher dans le tableau', 'tify')
        ];

        /**
         * @var array $columns
         * @var array $datas
         */
        $attrs = wp_parse_args($attrs, $defaults);
        extract($attrs);

        $count = count($columns);
        $num = 0;
        ?>
        <div class="tiFyTable">
            <?php
            if ($header) :
                self::tFyAppGetTemplatePart('head', null, compact('datas', 'columns', 'count', 'num'));
                reset($columns);
            endif;
            ?>

            <?php self::tFyAppGetTemplatePart('body', null, compact('datas', 'columns', 'count', 'num', 'none')); ?>
            <?php reset($columns); ?>

            <?php
            if ($footer) :
                self::tFyAppGetTemplatePart('foot', null, compact('datas', 'columns', 'count', 'num'));
                reset($columns);
            endif;
            ?>
        </div>
        <?php
    }
}