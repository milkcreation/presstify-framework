<?php

namespace tiFy\Template\Templates\PostListTable\Params;

use tiFy\Template\Templates\ListTable\Params\Params as BaseListTableParams;

class Params extends BaseListTableParams
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $defaults = array_merge(
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

        if ($this->template->request()->query('status') !== 'trash') :
            $defaults['bulk_actions'] = ['trash' => __('Déplacer dans la corbeille', 'tify')];
            $defaults['row_actions'] = ['edit', 'trash'];
        else :
            $defaults['bulk_actions'] = ['untrash', 'delete'];
            $defaults['row_actions'] = ['untrash', 'delete'];
        endif;

        return $defaults;
    }
}