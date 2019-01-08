<?php

namespace tiFy\View\Pattern\ListTable;

use tiFy\View\Pattern\ListTable\BulkActions\BulkActionsCollection;
use tiFy\View\Pattern\ListTable\BulkActions\BulkActionsItem;
use tiFy\View\Pattern\ListTable\BulkActions\BulkActionsItemTrash;
use tiFy\View\Pattern\ListTable\Columns\ColumnsCollection;
use tiFy\View\Pattern\ListTable\Columns\ColumnsItem;
use tiFy\View\Pattern\ListTable\Columns\ColumnsItemCb;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;
use tiFy\View\Pattern\ListTable\Items\Collection;
use tiFy\View\Pattern\ListTable\Items\Item;
use tiFy\View\Pattern\ListTable\Labels\Labels;
use tiFy\View\Pattern\ListTable\Pagination\Pagination;
use tiFy\View\Pattern\ListTable\Params\Params;
use tiFy\View\Pattern\ListTable\Request\Request;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsCollection;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsItem;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsItemActivate;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsItemDeactivate;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsItemDelete;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsItemDuplicate;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsItemEdit;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsItemPreview;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsItemTrash;
use tiFy\View\Pattern\ListTable\RowActions\RowActionsItemUntrash;
use tiFy\View\Pattern\ListTable\ViewFilters\ViewFiltersCollection;
use tiFy\View\Pattern\ListTable\ViewFilters\ViewFiltersItem;
use tiFy\View\Pattern\ListTable\Viewer\Viewer;
use tiFy\View\Pattern\PatternServiceProvider;
use tiFy\View\ViewEngine;

class ListTableServiceProvider extends PatternServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        array_push(
            $this->provides,
            'bulk-actions',
            'bulk-actions.item',
            'bulk-actions.item.trash',
            'columns',
            'columns.item',
            'columns.item.cb',
            'items',
            'item',
            'pagination',
            'row-actions',
            'row-actions.item',
            'row-actions.item.activate',
            'row-actions.item.deactivate',
            'row-actions.item.delete',
            'row-actions.item.duplicate',
            'row-actions.item.edit',
            'row-actions.item.preview',
            'row-actions.item.trash',
            'row-actions.item.untrash',
            'view-filters',
            'view-filters.item'
        );

        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        parent::register();

        $this->registerBulkActions();
        $this->registerColumns();
        $this->registerItems();
        $this->registerPagination();
        $this->registerRowActions();
        $this->registerViewFilters();
    }

    /**
     * Déclaration des controleurs d'actions groupées.
     *
     * @return void
     */
    public function registerBulkActions()
    {
        $this->getContainer()->share($this->getFullAlias('bulk-actions'), function (ListTable $pattern) {
            return new BulkActionsCollection($pattern->param('bulk_actions', []), $pattern);
        })->withArgument($this->pattern);

        $this->getContainer()->add($this->getFullAlias('bulk-actions.item'), BulkActionsItem::class);

        $this->getContainer()->add($this->getFullAlias('bulk-actions.item.trash'), BulkActionsItemTrash::class);
    }

    /**
     * Déclaration des controleurs de colonnes.
     *
     * @return void
     */
    public function registerColumns()
    {
        $this->getContainer()->share($this->getFullAlias('columns'), function (ListTable $pattern) {
            return new ColumnsCollection($pattern->param('columns', []), $pattern);
        })->withArgument($this->pattern);

        $this->getContainer()->add($this->getFullAlias('columns.item'), ColumnsItem::class);

        $this->getContainer()->add($this->getFullAlias('columns.item.cb'), ColumnsItemCb::class);
    }

    /**
     * Déclaration des controleurs d'éléments.
     *
     * @return void
     */
    public function registerItems()
    {
        $this->getContainer()->share($this->getFullAlias('items'), function ($items, ListTable $pattern) {
            return new Collection($items, $pattern);
        })->withArguments(
            [
                $this->pattern->config('items', []),
                $this->pattern
            ]
        );

        $this->getContainer()->add($this->getFullAlias('item'), Item::class);
    }

    /**
     * {@inheritdoc}
     */
    public function registerLabels()
    {
        $this->getContainer()->share($this->getFullAlias('labels'), function (ListTable $pattern) {
            return new Labels($pattern->name(), $pattern->config('labels', []), $pattern);
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration du controleur de pagination.
     *
     * @return void
     */
    public function registerPagination()
    {
        $this->getContainer()->share($this->getFullAlias('pagination'), function ($attrs, ListTable $pattern) {
            return new Pagination($attrs, $pattern);
        })->withArguments([[], $this->pattern]);
    }

    /**
     * {@inheritdoc}
     */
    public function registerParams()
    {
        $this->getContainer()->share($this->getFullAlias('params'), function (ListTable $pattern) {
            return new Params($pattern->config('params', []), $pattern);
        })->withArgument($this->pattern);
    }

    /**
     * {@inheritdoc}
     */
    public function registerRequest()
    {
        $this->getContainer()->share($this->getFullAlias('request'), function (ListTable $pattern) {
            return (Request::capture())->setPattern($pattern);
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration des controleurs d'action sur une ligne d'élément.
     *
     * @return void
     */
    public function registerRowActions()
    {
        $this->getContainer()->share($this->getFullAlias('row-actions'), function (ListTable $pattern) {
            return new RowActionsCollection($pattern->param('row_actions', []), $pattern);
        })->withArgument($this->pattern);

        $this->getContainer()->add($this->getFullAlias('row-actions.item'), RowActionsItem::class);

        $this->getContainer()->add($this->getFullAlias('row-actions.item.activate'), RowActionsItemActivate::class);

        $this->getContainer()->add(
            $this->getFullAlias('row-actions.item.deactivate'), RowActionsItemDeactivate::class
        );

        $this->getContainer()->add($this->getFullAlias('row-actions.item.delete'), RowActionsItemDelete::class);

        $this->getContainer()->add($this->getFullAlias('row-actions.item.duplicate'), RowActionsItemDuplicate::class);

        $this->getContainer()->add($this->getFullAlias('row-actions.item.edit'), RowActionsItemEdit::class);

        $this->getContainer()->add($this->getFullAlias('row-actions.item.preview'), RowActionsItemPreview::class);

        $this->getContainer()->add($this->getFullAlias('row-actions.item.trash'), RowActionsItemTrash::class);

        $this->getContainer()->add($this->getFullAlias('row-actions.item.untrash'), RowActionsItemUntrash::class);
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerViewer()
    {
        $this->getContainer()->share($this->getFullAlias('viewer'), function (ListTable $pattern) {
            $params = $pattern->config('viewer', []);

            if (!$params instanceof ViewEngine) :
                $viewer = new ViewEngine(
                    array_merge(
                        [
                            'directory' => pattern()->resourcesDir('/views/list-table')
                        ],
                        $params
                    )
                );
                $viewer->setController(Viewer::class);

                if (!$viewer->getOverrideDir()) :
                    $viewer->setOverrideDir(pattern()->resourcesDir('/views/list-table'));
                endif;
            else :
                $viewer = $params;
            endif;

            $viewer->set('pattern', $pattern);

            return $viewer;
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration des controleurs de filtre de la vue.
     *
     * @return void
     */
    public function registerViewFilters()
    {
        $this->getContainer()->share($this->getFullAlias('view-filters'), function (ListTable $pattern) {
            return new ViewFiltersCollection($pattern->param('view_filters', []), $pattern);
        })->withArgument($this->pattern);

        $this->getContainer()->add($this->getFullAlias('view-filters.item'), ViewFiltersItem::class);
    }
}