<?php

namespace tiFy\Layout\Admin\AjaxListTable;

use tiFy\Layout\Share\AjaxListTable\AjaxListTable as ShareAjaxListTable;

class AjaxListTable extends ShareAjaxListTable
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        add_action(
            'admin_enqueue_scripts',
            function () {
                \wp_enqueue_style(
                    'LayoutAdminAjaxListTable',
                    assets()->url('layout/admin/ajax-list-table/css/styles.css'),
                    ['datatables'],
                    160506
                );
                wp_enqueue_script(
                    'LayoutAdminAjaxListTable',
                    assets()->url('layout/admin/ajax-list-table/js/scripts.js'),
                    ['datatables'],
                    160506,
                    true
                );
            }
        );
    }
}