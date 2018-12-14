<?php

namespace tiFy\Wp\View\Pattern\PostListTable\Columns;

use tiFy\View\Pattern\ListTable\Columns\ColumnsItem;
use tiFy\View\Pattern\ListTable\Contracts\Item;

class ColumnsItemPostTitle extends ColumnsItem
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
    public function display(Item $item)
    {
        return "<strong>{$item->post_title}</strong>";
    }
}