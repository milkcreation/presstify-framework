<?php

namespace tiFy\Wp\View\Pattern\PostListTable\Columns;

use tiFy\View\Pattern\ListTable\Columns\ColumnsItem;

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
    public function content()
    {
        return ($item = $this->pattern->item())
            ? "<strong>{$item->post_title}</strong>"
            : '';
    }
}