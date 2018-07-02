<?php

namespace tiFy\Components\Layout\PostListTable;

use tiFy\Components\Db\DbPostsController;
use tiFy\Components\Layout\ListTable\ListTableServiceProvider;
use tiFy\Components\Layout\PostListTable\Param\ParamCollectionController;
use tiFy\Components\Layout\PostListTable\Column\ColumnItemPostTitleController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemAllController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemPublishController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemTrashController;
use tiFy\PostType\PostTypeLabelsController;

class PostListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return array_merge(
            parent::defaults(),
            [
                'columns.item.post_title' => [
                    'alias'     => ColumnItemPostTitleController::class,
                    'concrete'  => ColumnItemPostTitleController::class
                ],
                'view_filters.item.all' => [
                    'alias'     => ViewFilterItemAllController::class,
                    'concrete'  => ViewFilterItemAllController::class
                ],
                'view_filters.item.publish' => [
                    'alias'     => ViewFilterItemPublishController::class,
                    'concrete'  => ViewFilterItemPublishController::class
                ],
                'view_filters.item.trash' => [
                    'alias'     => ViewFilterItemTrashController::class,
                    'concrete'  => ViewFilterItemTrashController::class
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parseConcrete($key, $default)
    {
        switch($key) :
            default :
                return parent::parseConcrete($key, $default);
                break;
            case 'db' :
                return DbPostsController::class;
                break;
            case 'labels' :
                return PostTypeLabelsController::class;
                break;
            case 'params' :
                return ParamCollectionController::class;
                break;
        endswitch;
    }
}