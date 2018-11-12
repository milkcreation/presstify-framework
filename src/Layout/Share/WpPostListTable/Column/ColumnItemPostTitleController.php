<?php

namespace tiFy\Layout\Share\WpPostListTable\Column;

use tiFy\Layout\Share\ListTable\Column\ColumnItemController;

class ColumnItemPostTitleController extends ColumnItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'title' => __('Titre', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        return "<strong>{$item->post_title}</strong>";
    }
}