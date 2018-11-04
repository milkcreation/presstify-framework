<?php

namespace tiFy\Layout\Share\WpPostListTable\ViewFilter;

use tiFy\Layout\Share\ListTable\ViewFilter\ViewFilterItemController;

class ViewFilterItemTrashController extends ViewFilterItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $count = ($db = $this->layout->db())
            ? $db->select()->count(['status' => 'trash'])
            : 0;

        return [
            'content'     => __('Corbeille', 'tify'),
            'count_items' => $count,
            'hide_empty'  => true,
            'show_count'  => true,
            'query_args'  => ['post_status' => 'trash'],
            'current'     => $this->layout->request()->query('post_status') === 'trash'
        ];
    }
}