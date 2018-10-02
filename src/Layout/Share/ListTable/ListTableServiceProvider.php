<?php

namespace tiFy\Layout\Share\ListTable;

use tiFy\Layout\Share\ListTable\BulkAction\BulkActionCollectionController;
use tiFy\Layout\Share\ListTable\BulkAction\BulkActionItemController;
use tiFy\Layout\Share\ListTable\BulkAction\BulkActionItemTrashController;
use tiFy\Layout\Share\ListTable\Column\ColumnCollectionController;
use tiFy\Layout\Share\ListTable\Column\ColumnItemController;
use tiFy\Layout\Share\ListTable\Column\ColumnItemInterface;
use tiFy\Layout\Share\ListTable\Column\ColumnItemCbController;
use tiFy\Layout\Share\ListTable\Item\ItemCollectionController;
use tiFy\Layout\Share\ListTable\Item\ItemController;
use tiFy\Layout\Share\ListTable\Labels\LabelsController;
use tiFy\Layout\Share\ListTable\Pagination\PaginationController;
use tiFy\Layout\Share\ListTable\Params\ParamsController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionCollectionController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionItemController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionItemActivateController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionItemDeactivateController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionItemDeleteController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionItemDuplicateController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionItemEditController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionItemPreviewController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionItemTrashController;
use tiFy\Layout\Share\ListTable\RowAction\RowActionItemUntrashController;
use tiFy\Layout\Share\ListTable\ViewFilter\ViewFilterCollectionController;
use tiFy\Layout\Share\ListTable\ViewFilter\ViewFilterItemController;
use tiFy\Layout\Share\ListTable\ViewFilter\ViewFilterItemInterface;
use tiFy\Layout\Share\ListTable\Request\RequestController;
use tiFy\Layout\Base\ServiceProvider;

class ListTableServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->getContainer()->bind('bulk_actions', BulkActionCollectionController::class);
        $this->getContainer()->bind('bulk_actions.item', BulkActionItemController::class);
        $this->getContainer()->bind('bulk_actions.item.trash', BulkActionItemTrashController::class);

        $this->getContainer()->singleton('columns', ColumnCollectionController::class);
        $this->getContainer()->bind('columns.item', ColumnItemController::class);
        $this->getContainer()->bind('columns.item.cb', ColumnItemCbController::class);

        $this->getContainer()->singleton('items', ItemCollectionController::class);
        $this->getContainer()->bind('item', ItemController::class);

        $this->getContainer()->singleton('labels', LabelsController::class);

        $this->getContainer()->singleton('pagination', PaginationController::class);

        $this->getContainer()->singleton('params', ParamsController::class);

        $this->getContainer()->singleton('request', RequestController::class);

        $this->getContainer()->bind('row_actions', RowActionCollectionController::class);
        $this->getContainer()->bind('row_actions.item', RowActionItemController::class);
        $this->getContainer()->bind('row_actions.item.activate', RowActionItemActivateController::class);
        $this->getContainer()->bind('row_actions.item.deactivate', RowActionItemDeactivateController::class);
        $this->getContainer()->bind('row_actions.item.delete', RowActionItemDeleteController::class);
        $this->getContainer()->bind('row_actions.item.duplicate', RowActionItemDuplicateController::class);
        $this->getContainer()->bind('row_actions.item.edit', RowActionItemEditController::class);
        $this->getContainer()->bind('row_actions.item.preview', RowActionItemPreviewController::class);
        $this->getContainer()->bind('row_actions.item.trash', RowActionItemTrashController::class);
        $this->getContainer()->bind('row_actions.item.untrash', RowActionItemUntrashController::class);

        $this->getContainer()->singleton('view_filters', ViewFilterCollectionController::class);
        $this->getContainer()->bind('view_filters.item', ViewFilterItemController::class);
    }
}