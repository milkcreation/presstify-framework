<?php

namespace tiFy\Components\Layout\PostListTable\ViewFilter;

use tiFy\Components\Layout\ListTable\ViewFilter\ViewFilterItemController;

class ViewFilterItemAllController extends ViewFilterItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $count = ($db = $this->app->getDb())
            ? $db->select()->count(
                [
                    'status' => [
                        'publish',
                        'pending',
                        'draft',
                        'auto-draft',
                        'future',
                        'private',
                        'inherit'
                    ]
                ])
            : 0;

        return [
            'content'           => __('Tous', 'tify'),
            'count_items'       => $count,
            'show_count'        => true,
            'remove_query_args' => 'status',
            'current'           => !$this->app->appRequest()->get('status', '')
        ];
    }
}