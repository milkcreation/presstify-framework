<?php

namespace tiFy\Wp\View\Pattern\PostListTable\ViewFilters;

use tiFy\View\Pattern\ListTable\ViewFilters\ViewFiltersItem;

class ViewFiltersItemAll extends ViewFiltersItem
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $count = ($db = $this->pattern->db())
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
            'current'           => !$this->pattern->request()->query('post_status', '')
        ];
    }
}