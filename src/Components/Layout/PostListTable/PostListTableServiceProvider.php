<?php

namespace tiFy\Components\Layout\PostListTable;

use tiFy\Components\Layout\ListTable\ListTableServiceProvider;
use tiFy\Components\Layout\PostListTable\Column\ColumnItemPostTitleController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemAllController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemPublishController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemTrashController;

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
                    'concrete'  => $this->app->getConcrete('columns.item.post_title', ColumnItemPostTitleController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'view_filters.item.all' => [
                    'alias'     => ViewFilterItemAllController::class,
                    'concrete'  => $this->app->getConcrete('view_filters.item.all', ViewFilterItemAllController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'view_filters.item.publish' => [
                    'alias'     => ViewFilterItemPublishController::class,
                    'concrete'  => $this->app->getConcrete('view_filters.item.all', ViewFilterItemPublishController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'view_filters.item.trash' => [
                    'alias'     => ViewFilterItemTrashController::class,
                    'concrete'  => $this->app->getConcrete('view_filters.item.all', ViewFilterItemTrashController::class),
                    'bootable'  => false,
                    'singleton' => false
                ]
            ]
        );
    }
}