<?php

namespace tiFy\Components\AdminView\AjaxListTable;

use tiFy\Components\Layout\AjaxListTable\AjaxListTable as LayoutAjaxListTable;

class AjaxListTable extends LayoutAjaxListTable
{
    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        \wp_enqueue_style(
            'tiFyAdminView-AjaxListTable',
            $this->appAsset('/AdminView/AjaxListTable/css/styles.css'),
            ['datatables'],
            160506
        );
        wp_enqueue_script(
            'tiFyAdminView-AjaxListTable',
            $this->appAsset('/AdminView/AjaxListTable/js/scripts.js'),
            ['datatables'],
            160506,
            true
        );
    }
}