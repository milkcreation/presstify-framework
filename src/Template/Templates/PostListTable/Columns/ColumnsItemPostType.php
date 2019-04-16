<?php

namespace tiFy\Template\Templates\PostListTable\Columns;

use tiFy\Template\Templates\ListTable\Columns\ColumnsItem;

class ColumnsItemPostType extends ColumnsItem
{
    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'title' => __('Type', 'tify')
        ];
    }

    /**
     * @inheritdoc
     */
    public function content(): string
    {
        if ($item = $this->factory->item()) :
            return ($postType = post_type($item->post_type))
                ? $postType->label('singular_name')
                : "{$item->post_type}";
        else :
            return "";
        endif;


    }
}