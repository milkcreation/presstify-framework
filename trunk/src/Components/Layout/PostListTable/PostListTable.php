<?php

namespace tiFy\Components\Layout\PostListTable;

use tiFy\Components\Layout\ListTable\ListTable;
use tiFy\Components\Layout\PostListTable\Column\ColumnItemPostTitleController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemAllController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemPublishController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemTrashController;

class PostListTable extends ListTable
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $providers = [
        PostListTableServiceProvider::class
    ];

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return array_merge(
            parent::getAliases(),
            [
                'columns.item.post_title'   => ColumnItemPostTitleController::class,
                'view_filters.item.all'     => ViewFilterItemAllController::class,
                'view_filters.item.publish' => ViewFilterItemPublishController::class,
                'view_filters.item.trash'   => ViewFilterItemTrashController::class,
            ]
        );
    }
}