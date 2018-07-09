<?php

namespace tiFy\Components\Layout\ListTable;

use tiFy\Apps\ServiceProvider\AbstractProviderCollection;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionController;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionInterface;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemController;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemInterface;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemTrashController;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemTrashInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionController;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnItemController;
use tiFy\Components\Layout\ListTable\Column\ColumnItemInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnItemCbController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\Layout\ListTable\Item\ItemController;
use tiFy\Components\Layout\ListTable\Item\ItemInterface;
use tiFy\Components\Layout\ListTable\Labels\LabelsController;
use tiFy\Components\Layout\ListTable\Pagination\PaginationController;
use tiFy\Components\Layout\ListTable\Pagination\PaginationInterface;
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
use tiFy\Components\Layout\ListTable\Request\RequestController;
use tiFy\Apps\Layout\LayoutControllerInterface;
use tiFy\Apps\Layout\LayoutServiceProvider;

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
                    'concrete'  => ColumnCollectionController::class,
                    'singleton' => true
                ],
                'columns.item' => [
                    'alias'     => ColumnItemInterface::class,
                    'concrete'  => ColumnItemController::class
                ],
                'columns.item.cb' => [
                    'alias'     => ColumnItemCbController::class,
                    'concrete'  => ColumnItemCbController::class
                ],
                'bulk_actions' => [
                    'alias'     => BulkActionCollectionInterface::class,
                    'concrete'  => BulkActionCollectionController::class
                ],
                'bulk_actions.item' => [
                    'alias'     => BulkActionItemInterface::class,
                    'concrete'  => BulkActionItemController::class
                ],
                'bulk_actions.item.trash' => [
                    'alias'     => BulkActionItemTrashInterface::class,
                    'concrete'  => BulkActionItemTrashController::class
                ],
                'items' => [
                    'alias'     => ItemCollectionInterface::class,
                    'concrete'  => ItemCollectionController::class,
                    'singleton' => true
                ],
                'item' => [
                    'alias'     => ItemInterface::class,
                    'concrete'  => ItemController::class
                ],
                'pagination' => [
                    'alias'     => PaginationInterface::class,
                    'concrete'  => PaginationController::class,
                    'singleton' => true
                ],
                'row_actions' => [
                    'alias'     => RowActionCollectionInterface::class,
                    'concrete'  => RowActionCollectionController::class
                ],
                'row_actions.item' => [
                    'alias'     => RowActionItemInterface::class,
                    'concrete'  => RowActionItemController::class
                ],
                'row_actions.item.activate' => [
                    'alias'     => RowActionItemActivateController::class,
                    'concrete'  => RowActionItemActivateController::class
                ],
                'row_actions.item.deactivate' => [
                    'alias'     => RowActionItemDeactivateController::class,
                    'concrete'  => RowActionItemDeactivateController::class
                ],
                'row_actions.item.delete' => [
                    'alias'     => RowActionItemDeleteController::class,
                    'concrete'  => RowActionItemDeleteController::class
                ],
                'row_actions.item.duplicate' => [
                    'alias'     => RowActionItemDuplicateController::class,
                    'concrete'  => RowActionItemDuplicateController::class
                ],
                'row_actions.item.edit' => [
                    'alias'     => RowActionItemEditController::class,
                    'concrete'  => RowActionItemEditController::class
                ],
                'row_actions.item.preview' => [
                    'alias'     => RowActionItemPreviewController::class,
                    'concrete'  => RowActionItemPreviewController::class
                ],
                'row_actions.item.trash' => [
                    'alias'     => RowActionItemTrashController::class,
                    'concrete'  => RowActionItemTrashController::class
                ],
                'row_actions.item.untrash' => [
                    'alias'     => RowActionItemUntrashController::class,
                    'concrete'  => RowActionItemUntrashController::class
                ],
                'view_filters' => [
                    'alias'     => ViewFilterCollectionInterface::class,
                    'concrete'  => ViewFilterCollectionController::class,
                    'singleton' => true
                ],
                'view_filters.item' => [
                    'alias'     => ViewFilterItemInterface::class,
                    'concrete'  => ViewFilterItemController::class
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
            case 'labels' :
                return LabelsController::class;
                break;
            case 'params' :
                return ParamCollectionController::class;
                break;
            case 'request' :
                return RequestController::class;
                break;
        endswitch;
    }
}