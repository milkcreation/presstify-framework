<?php

namespace tiFy\Layout\Share\WpUserListTable\Column;

use tiFy\Layout\Share\ListTable\Column\ColumnItemController;

class ColumnItemRoleController extends ColumnItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'title' => __('Rôle', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        global $wp_roles;

        $user_role = current($item->roles);
        $role_link = esc_url(add_query_arg('role', $user_role, $this->layout->request()->sanitizeUrl()));

        return isset($wp_roles->role_names[$user_role]) ? "<a href=\"{$role_link}\">" . translate_user_role($wp_roles->role_names[$user_role]) . "</a>" : __('Aucun', 'tify');
    }
}