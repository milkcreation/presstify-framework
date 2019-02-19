<?php

namespace tiFy\Template\Templates\PostListTable\ViewFilters;

use tiFy\Template\Templates\ListTable\ViewFilters\ViewFiltersItem;

class ViewFiltersItemPublish extends ViewFiltersItem
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $count = ($db = $this->template->db())
            ? $db->select()->count(['status' => 'publish'])
            : 0;

        return [
            'content'     => _n('Publié', 'Publiés', ($count > 1 ? 2 : 1), 'tify'),
            'count_items' => $count,
            'show_count'  => true,
            'query_args'  => ['post_status' => 'publish'],
            'current'     => $this->template->request()->query('post_status') === 'publish'
        ];
    }
}