<?php

namespace tiFy\Components\Layout\ListTable;

use tiFy\App\Layout\Labels\LabelsInterface;
use tiFy\App\Layout\Params\ParamsInterface;
use tiFy\App\Layout\Request\RequestInterface;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionController;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionInterface;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemController;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemInterface;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemTrashController;
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
use tiFy\Components\Layout\ListTable\Params\ParamsController;
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
use tiFy\Components\Layout\ListTable\Request\RequestController;
use tiFy\App\Layout\LayoutServiceProvider;

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
    public function boot()
    {
        parent::boot();

        $this->app->singleton(LabelsInterface::class, function ($app) {
            return new LabelsController($app->getName(), [], $app);
        });

        $this->app->singleton(ParamsInterface::class, function ($app) {
            return new ParamsController($app->get('params', []), $app);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return array_merge(
            parent::getBindings(),
            [
                ColumnItemInterface::class           => ColumnItemController::class,
                ColumnItemCbController::class,
                BulkActionCollectionInterface::class => BulkActionCollectionController::class,
                BulkActionItemInterface::class       => BulkActionItemController::class,
                BulkActionItemTrashController::class,
                ItemInterface::class                 => ItemController::class,
                RowActionCollectionInterface::class  => RowActionCollectionController::class,
                RowActionItemInterface::class        => RowActionItemController::class,
                RowActionItemActivateController::class,
                RowActionItemDeactivateController::class,
                RowActionItemDeleteController::class,
                RowActionItemDuplicateController::class,
                RowActionItemEditController::class,
                RowActionItemPreviewController::class,
                RowActionItemTrashController::class,
                RowActionItemUntrashController::class,
                ViewFilterItemInterface::class       => ViewFilterItemController::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSingletons()
    {
        return array_merge(
            parent::getSingletons(),
            [
                ColumnCollectionInterface::class     => ColumnCollectionController::class,
                ItemCollectionInterface::class       => ItemCollectionController::class,
                PaginationInterface::class           => PaginationController::class,
                RequestInterface::class              => RequestController::class,
                ViewFilterCollectionInterface::class => ViewFilterCollectionController::class,
            ]
        );
    }
}