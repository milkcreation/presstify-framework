<?php

namespace tiFy\Components\Layout\UserListTable\Column;

use tiFy\Components\Layout\ListTable\Column\ColumnItemController;

class ColumnItemUserLoginController extends ColumnItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'title' => __('Identifiant', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        $avatar = get_avatar($item->ID, 32);

        if (current_user_can('edit_user', $item->ID) && $this->EditBaseUri) :
            return sprintf('%1$s<strong>%2$s</strong>', $avatar,
                $this->get_item_edit_link($item, [], $item->user_login));
        else :
            return sprintf('%1$s<strong>%2$s</strong>', $avatar, $item->user_login);
        endif;
    }
}