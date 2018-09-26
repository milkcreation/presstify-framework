<?php

namespace tiFy\Layout\Share\WpPostListTable\ViewFilter;

use tiFy\Layout\Share\ListTable\ViewFilter\ViewFilterItemController;

class ViewFilterItemAllController extends ViewFilterItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $count = ($db = $this->layout->db())
            ? $db->select()->count(
                [
                    'post_type'   => array_diff(
                        array_keys(get_post_types()),
                        [
                            'attachment',
                            'custom_css',
                            'customize_changeset',
                            'nav_menu_item',
                            'oembed_cache',
                            'revision',
                            'user_request'
                        ]
                    ),
                    'status' => [
                        'publish',
                        'pending',
                        'draft',
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
            'remove_query_args' => ['post_status'],
            'current'           => !$this->layout->request()->query('post_status', '')
        ];
    }
}