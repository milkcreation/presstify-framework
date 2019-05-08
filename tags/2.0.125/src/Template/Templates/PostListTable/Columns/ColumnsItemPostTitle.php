<?php

namespace tiFy\Template\Templates\PostListTable\Columns;

use tiFy\Template\Templates\ListTable\Columns\ColumnsItem;

class ColumnsItemPostTitle extends ColumnsItem
{
    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'title' => __('Titre', 'tify')
        ];
    }

    /**
     * @inheritdoc
     */
    public function content(): string
    {
        return ($item = $this->factory->item())
            ? "<strong>{$item->post_title}</strong>"
            : '';
    }
}