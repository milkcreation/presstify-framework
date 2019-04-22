<?php

namespace tiFy\Template\Templates\PostListTable\ViewFilters;

use tiFy\Template\Templates\ListTable\ViewFilters\ViewFiltersItem;

class ViewFiltersItemTrash extends ViewFiltersItem
{
    /**
     * @inheritdoc
     */
    public function defaults()
    {
        $count = ($db = $this->factory->db())
            ? $db->select()->count(['status' => 'trash'])
            : 0;

        return [
            'content'     => __('Corbeille', 'tify'),
            'count_items' => $count,
            'hide_empty'  => true,
            'show_count'  => true,
            'query_args'  => ['post_status' => 'trash'],
            'current'     => $this->factory->request()->query('post_status') === 'trash'
        ];
    }
}