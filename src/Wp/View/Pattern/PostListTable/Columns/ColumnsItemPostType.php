<?php

namespace tiFy\Wp\View\Pattern\PostListTable\Columns;

use tiFy\View\Pattern\ListTable\Columns\ColumnsItem;

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
    public function content()
    {
        if ($item = $this->pattern->item()) :
            return ($postType = post_type($item->post_type))
                ? $postType->label('singular_name')
                : "{$item->post_type}";
        else :
            return "";
        endif;


    }
}