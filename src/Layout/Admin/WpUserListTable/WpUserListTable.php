<?php

namespace tiFy\Layout\Admin\WpUserListTable;

use tiFy\Layout\Share\WpUserListTable\WpUserListTable as ShareWpUserListTable;

class WpUserListTable extends ShareWpUserListTable
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
                wp_enqueue_style(
                    'LayoutAdminWpUserListTable',
                    assets()->url('layout/admin/user-list-table/css/styles.css'),
                    [],
                    171115
                );
            }
        );
    }
}