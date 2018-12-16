<?php

namespace tiFy\Wp\View\Pattern\PostListTable\Params;

use tiFy\View\Pattern\ListTable\Params\Params as BaseListTableParams;

class Params extends BaseListTableParams
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return array_merge(
            parent::defaults(),
            [
                'columns'      => [
                    'cb',
                    'post_title',
                    'post_type',
                    'post_date' => __('Date', 'tify'),
                ],
                'view_filters' => [
                    'all',
                    'publish',
                    'trash'
                ],
                'query_args'   => [
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
                    'post_status' => ['publish', 'pending', 'draft', 'future', 'private', 'inherit'],
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        if ($this->pattern->request()->query('status') !== 'trash') :
            $attrs['bulk_actions'] = ['trash' => __('Déplacer dans la corbeille', 'tify')];
            $attrs['row_actions'] = ['edit', 'trash'];
        else :
            $attrs['bulk_actions'] = ['untrash', 'delete'];
            $attrs['row_actions'] = ['untrash', 'delete'];
        endif;

        parent::parse($attrs);
    }
}