<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use tiFy\Template\Templates\ListTable\ViewFilter;

class ViewFilterAll extends ViewFilter
{
    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        $count = ($db = $this->factory->db())
            ? $db->whereIn(
                'post_type',
                array_diff(
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
                )
            )->whereIn('posts_status', [
                'publish',
                'pending',
                'draft',
                'future',
                'private',
                'inherit'
            ])->count() : 0;

        return [
            'content'           => __('Tous', 'tify'),
            'count_items'       => $count,
            'show_count'        => true,
            'remove_query_args' => ['post_status'],
            'current'           => !$this->factory->request()->input('post_status', '')
        ];
    }
}