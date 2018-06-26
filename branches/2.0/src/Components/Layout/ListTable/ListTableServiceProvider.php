<?php

namespace tiFy\Components\Layout\ListTable;

use tiFy\Apps\ServiceProvider\AbstractProviderCollection;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionController;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionController;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnItemController;
use tiFy\Components\Layout\ListTable\Column\ColumnItemInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnItemCbController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\Layout\ListTable\RowAction\RowActionCollectionController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionCollectionInterface;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemInterface;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemActivateController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemDeactivateController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemDeleteController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemDuplicateController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemEditController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemPreviewController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemTrashController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemUntrashController;
use tiFy\Components\Layout\ListTable\ViewFilter\ViewFilterCollectionController;
use tiFy\Components\Layout\ListTable\ViewFilter\ViewFilterCollectionInterface;
use tiFy\Components\Layout\ListTable\ViewFilter\ViewFilterItemController;
use tiFy\Components\Layout\ListTable\ViewFilter\ViewFilterItemInterface;
use tiFy\Components\Layout\ListTable\ListTableInterface;
use tiFy\Components\Layout\ListTable\Param\ParamCollectionController;
use tiFy\Kernel\Layout\LayoutControllerInterface;
use tiFy\Kernel\Layout\LayoutServiceProvider;

class ListTableServiceProvider extends LayoutServiceProvider
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var ListTableInterface
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return array_merge(
            parent::defaults(),
            [
                'columns' => [
                    'alias'     => ColumnCollectionInterface::class,
                    'concrete'  => $this->app->getConcrete('columns', ColumnCollectionController::class),
                    'bootable'  => false,
                    'singleton' => true
                ],
                'columns.item' => [
                    'alias'     => ColumnItemInterface::class,
                    'concrete'  => $this->app->getConcrete('columns.item', ColumnItemController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'columns.item.cb' => [
                    'alias'     => ColumnItemCbController::class,
                    'concrete'  => $this->app->getConcrete('columns.item.cb', ColumnItemCbController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'bulk_actions' => [
                    'alias'     => BulkActionCollectionInterface::class,
                    'concrete'  => $this->app->getConcrete('bulk_actions', BulkActionCollectionController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'items' => [
                    'alias'     => ItemCollectionInterface::class,
                    'concrete'  => $this->app->getConcrete('items', ItemCollectionController::class),
                    'bootable'  => false,
                    'singleton' => true
                ],
                'row_actions' => [
                    'alias'     => RowActionCollectionInterface::class,
                    'concrete'  => $this->app->getConcrete('row_actions', RowActionCollectionController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'row_actions.item' => [
                    'alias'     => RowActionItemInterface::class,
                    'concrete'  => $this->app->getConcrete('row_actions', RowActionItemController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'row_actions.item.activate' => [
                    'alias'     => RowActionItemActivateController::class,
                    'concrete'  => $this->app->getConcrete('row_actions.item.activate', RowActionItemActivateController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'row_actions.item.deactivate' => [
                    'alias'     => RowActionItemDeactivateController::class,
                    'concrete'  => $this->app->getConcrete('row_actions.item.activate', RowActionItemDeactivateController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'row_actions.item.delete' => [
                    'alias'     => RowActionItemDeleteController::class,
                    'concrete'  => $this->app->getConcrete('row_actions.item.activate', RowActionItemDeleteController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'row_actions.item.duplicate' => [
                    'alias'     => RowActionItemDuplicateController::class,
                    'concrete'  => $this->app->getConcrete('row_actions.item.activate', RowActionItemDuplicateController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'row_actions.item.edit' => [
                    'alias'     => RowActionItemEditController::class,
                    'concrete'  => $this->app->getConcrete('row_actions.item.activate', RowActionItemEditController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'row_actions.item.preview' => [
                    'alias'     => RowActionItemPreviewController::class,
                    'concrete'  => $this->app->getConcrete('row_actions.item.activate', RowActionItemPreviewController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'row_actions.item.trash' => [
                    'alias'     => RowActionItemTrashController::class,
                    'concrete'  => $this->app->getConcrete('row_actions.item.activate', RowActionItemTrashController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'row_actions.item.untrash' => [
                    'alias'     => RowActionItemUntrashController::class,
                    'concrete'  => $this->app->getConcrete('row_actions.item.activate', RowActionItemUntrashController::class),
                    'bootable'  => false,
                    'singleton' => false
                ],
                'view_filters' => [
                    'alias'     => ViewFilterCollectionInterface::class,
                    'concrete'  => $this->app->getConcrete('view_filters', ViewFilterCollectionController::class),
                    'bootable'  => false,
                    'singleton' => true
                ],
                'view_filters.item' => [
                    'alias'     => ViewFilterItemInterface::class,
                    'concrete'  => $this->app->getConcrete('view_filters.item', ViewFilterItemController::class),
                    'bootable'  => false,
                    'singleton' => false
                ]
            ]
        );
    }
}