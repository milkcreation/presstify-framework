<?php

namespace tiFy\Layout\Share\WpPostListTable\ViewFilter;

use tiFy\Layout\Share\ListTable\ViewFilter\ViewFilterItemController;

class ViewFilterItemPublishController extends ViewFilterItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $count = ($db = $this->layout->db())
            ? $db->select()->count(['status' => 'publish'])
            : 0;

        return [
            'content'     => _n('Publié', 'Publiés', ($count > 1 ? 2 : 1), 'tify'),
            'count_items' => $count,
            'show_count'  => true,
            'query_args'  => ['post_status' => 'publish'],
            'current'     => $this->layout->request()->query('post_status') === 'publish'
        ];
    }
}