<?php

namespace tiFy\Template\Templates\PostListTable\Columns;

use tiFy\Template\Templates\ListTable\Columns\ColumnsItem;

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
        return ($item = $this->template->item())
            ? "<strong>{$item->post_title}</strong>"
            : '';
    }
}