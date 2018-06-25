<?php

namespace tiFy\Components\AdminView\PostListTable\ViewFilter;

use tiFy\Components\AdminView\ListTable\ViewFilter\ViewFilterItemController;

class ViewFilterItemTrashController extends ViewFilterItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $count = ($db = $this->app->getDb())
            ? $db->select()->count(['status' => 'trash'])
            : 0;

        return [
            'content'     => __('Corbeille', 'tify'),
            'count_items' => $count,
            'hide_empty'  => true,
            'show_count'  => true,
            'query_args'  => ['status' => 'trash'],
            'current'     => $this->app->appRequest()->get('status', '') === 'trash'
        ];
    }
}