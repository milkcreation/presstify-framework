<?php

namespace tiFy\Components\AdminView\PostListTable\ViewFilter;

use tiFy\Components\AdminView\ListTable\ViewFilter\ViewFilterItemController;

class ViewFilterItemPublishController extends ViewFilterItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $count = ($db = $this->app->getDb())
            ? $db->select()->count(['status' => 'publish'])
            : 0;

        return [
            'content'     => _n('Publié', 'Publiés', ($count > 1 ? 2 : 1), 'tify'),
            'count_items' => $count,
            'show_count'  => true,
            'query_args'  => ['status' => 'publish'],
            'current'     => $this->app->appRequest()->get('status', '') === 'publish'
        ];
    }
}