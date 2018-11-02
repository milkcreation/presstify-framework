<?php

namespace tiFy\Layout\Share\WpUserListTable\Column;

use tiFy\Layout\Share\ListTable\Column\ColumnItemController;

class ColumnItemUserRegisteredController extends ColumnItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'title' => __('Enregistrement', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        return mysql2date(__('d/m/Y à H:i', 'tify'), $item->user_registered, true);
    }
}