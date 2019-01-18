<?php

namespace tiFy\Template\Templates\PostListTable\ViewFilters;

use tiFy\Template\Templates\ListTable\ViewFilters\ViewFiltersItem;

class ViewFiltersItemAll extends ViewFiltersItem
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $count = ($db = $this->template->db())
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
            'current'           => !$this->template->request()->query('post_status', '')
        ];
    }
}