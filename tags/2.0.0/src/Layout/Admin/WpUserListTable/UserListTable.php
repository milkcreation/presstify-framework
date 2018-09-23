<?php

namespace tiFy\Components\AdminView\UserListTable;

use tiFy\Components\Layout\UserListTable\UserListTable as LayoutUserListTable;

class UserListTable extends LayoutUserListTable
{
    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        \wp_enqueue_style(
            'tiFyAdminViewUserListTable',
            $this->appAssetUrl('AdminView/UserListTable/css/styles.css'),
            [],
            171115
        );
    }
}