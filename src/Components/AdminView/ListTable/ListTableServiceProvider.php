<?php

namespace tiFy\Components\AdminView\ListTable;

use tiFy\AdminView\AdminViewServiceProvider;
use tiFy\Apps\ServiceProvider\AbstractProviderCollection;
use tiFy\Components\AdminView\ListTable\BulkAction\BulkActionCollectionController;
use tiFy\Components\AdminView\ListTable\BulkAction\BulkActionCollectionInterface;
use tiFy\Components\AdminView\ListTable\Column\ColumnCollectionController;
use tiFy\Components\AdminView\ListTable\Column\ColumnCollectionInterface;
use tiFy\Components\AdminView\ListTable\Item\ItemCollectionController;
use tiFy\Components\AdminView\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionCollectionController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionCollectionInterface;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemInterface;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemActivateController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemDeactivateController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemDeleteController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemDuplicateController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemEditController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemPreviewController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemTrashController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionItemUntrashController;
use tiFy\Components\AdminView\ListTable\ViewFilter\ViewFilterCollectionController;
use tiFy\Components\AdminView\ListTable\ViewFilter\ViewFilterCollectionInterface;
use tiFy\Components\AdminView\ListTable\ViewFilter\ViewFilterItemController;
use tiFy\Components\AdminView\ListTable\ViewFilter\ViewFilterItemInterface;
use tiFy\Components\AdminView\ListTable\ListTableInterface;
use tiFy\Components\AdminView\ListTable\Param\ParamCollectionController;

class ListTableServiceProvider extends AdminViewServiceProvider
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
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
                    'singleton' => true,
                    'args'      => [$this->app]
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
                    'singleton' => true,
                    'args'      => [$this->app]
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
                    'singleton' => true,
                    'args'      => [$this->app]
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