<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use tiFy\Template\Templates\ListTable\ViewFilter;

class ViewFilterTrash extends ViewFilter
{
    /**
     * @inheritDoc
     */
    public function defaults() : array
    {
        $count = ($db = $this->factory->db())
            ? $db->where('post_status', 'trash')->count()
            : 0;

        return [
            'content'     => __('Corbeille', 'tify'),
            'count_items' => $count,
            'hide_empty'  => true,
            'show_count'  => true,
            'query_args'  => ['post_status' => 'trash'],
            'current'     => $this->factory->request()->input('post_status') === 'trash'
        ];
    }
}