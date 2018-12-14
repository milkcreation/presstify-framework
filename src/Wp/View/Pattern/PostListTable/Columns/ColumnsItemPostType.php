<?php

namespace tiFy\Wp\View\Pattern\PostListTable\Columns;

use tiFy\View\Pattern\ListTable\Columns\ColumnsItem;
use tiFy\View\Pattern\ListTable\Contracts\Item;

class ColumnsItemPostType extends ColumnsItem
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'title' => __('Type', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display(Item $item)
    {
        return ($postType = post_type($item->post_type))
            ? $postType->label('singular_name')
            : "{$item->post_type}";
    }
}