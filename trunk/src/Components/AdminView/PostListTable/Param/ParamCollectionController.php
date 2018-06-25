<?php

namespace tiFy\Components\AdminView\PostListTable\Param;

use tiFy\Components\AdminView\ListTable\Param\ParamCollectionController as ListTableParamCollectionController;
use tiFy\Components\AdminView\PostListTable\Column\ColumnItemPostTitleController;
use tiFy\Components\AdminView\PostListTable\ViewFilter\ViewFilterItemAllController;
use tiFy\Components\AdminView\PostListTable\ViewFilter\ViewFilterItemPublishController;
use tiFy\Components\AdminView\PostListTable\ViewFilter\ViewFilterItemTrashController;

class ParamCollectionController extends ListTableParamCollectionController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $attrs = array_merge(
            parent::defaults(),
            [
                'columns'      => [
                    'cb',
                    'post_title' => [
                        'controller' => ColumnItemPostTitleController::class,
                    ],
                    'post_type'  => __('Type de post', 'tify'),
                    'post_date'  => __('Date', 'tify'),
                ],
                'view_filters' => [
                    'all' => [
                        'controller' => ViewFilterItemAllController::class
                    ],
                    'publish' => [
                        'controller' => ViewFilterItemPublishController::class
                    ],
                    'trash' => [
                        'controller' => ViewFilterItemTrashController::class
                    ]
                ],
                'query_args'   => [
                    'status' => ['publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'],
                ],
            ]
        );

        if ($this->app->appRequest()->get('status') !== 'trash') :
            $attrs['bulk_actions'] = ['trash' => __('Déplacer dans la corbeille', 'tify')];
            $attrs['row_actions'] = ['edit', 'trash'];
        else :
            $attrs['bulk_actions'] = ['untrash', 'delete'];
            $attrs['row_actions'] = ['untrash', 'delete'];
        endif;

        return $attrs;
    }
}