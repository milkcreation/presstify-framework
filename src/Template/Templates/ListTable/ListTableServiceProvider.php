<?php

namespace tiFy\Template\Templates\ListTable;

use tiFy\Template\Templates\ListTable\BulkActions\BulkActionsCollection;
use tiFy\Template\Templates\ListTable\BulkActions\BulkActionsItem;
use tiFy\Template\Templates\ListTable\BulkActions\BulkActionsItemTrash;
use tiFy\Template\Templates\ListTable\Columns\ColumnsCollection;
use tiFy\Template\Templates\ListTable\Columns\ColumnsItem;
use tiFy\Template\Templates\ListTable\Columns\ColumnsItemCb;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use tiFy\Template\Templates\ListTable\Items\Collection;
use tiFy\Template\Templates\ListTable\Items\Item;
use tiFy\Template\Templates\ListTable\Labels\Labels;
use tiFy\Template\Templates\ListTable\Pagination\Pagination;
use tiFy\Template\Templates\ListTable\Params\Params;
use tiFy\Template\Templates\ListTable\Request\Request;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsCollection;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsItem;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsItemActivate;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsItemDeactivate;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsItemDelete;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsItemDuplicate;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsItemEdit;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsItemPreview;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsItemTrash;
use tiFy\Template\Templates\ListTable\RowActions\RowActionsItemUntrash;
use tiFy\Template\Templates\ListTable\ViewFilters\ViewFiltersCollection;
use tiFy\Template\Templates\ListTable\ViewFilters\ViewFiltersItem;
use tiFy\Template\Templates\ListTable\Viewer\Viewer;
use tiFy\Template\Templates\BaseServiceProvider;
use tiFy\View\ViewEngine;

class ListTableServiceProvider extends BaseServiceProvider
{
    /**
     * @inheritdoc
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
     * @inheritdoc
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
        $this->getContainer()->share($this->getFullAlias('bulk-actions'), function (ListTable $template) {
            return new BulkActionsCollection($template->param('bulk_actions', []), $template);
        })->withArgument($this->template);

        $this->getContainer()->add($this->getFullAlias('bulk-actions.item'), BulkActionsItem::class);

        $this->getContainer()->add($this->getFullAlias('bulk-actions.item.trash'), BulkActionsItemTrash::class);
    }

    /**
     * Déclaration des controleurs de colonnes de la table.
     *
     * @return void
     */
    public function registerColumns()
    {
        $this->getContainer()->share($this->getFullAlias('columns'), function (ListTable $template) {
            if (!$columns = $template->param('columns', [])) :
                $columns = ($this->getContainer()->has($this->getFullAlias('db')))
                    ? $this->getContainer()->get($this->getFullAlias('db'))->getColNames() : [];
            endif;

            return new ColumnsCollection($columns, $template);
        })->withArgument($this->template);

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
        $this->getContainer()->share($this->getFullAlias('items'), function ($items, ListTable $template) {
            return new Collection($items, $template);
        })->withArguments(
            [
                $this->template->config('items', []),
                $this->template
            ]
        );

        $this->getContainer()->add($this->getFullAlias('item'), Item::class);
    }

    /**
     * @inheritdoc
     */
    public function registerLabels()
    {
        $this->getContainer()->share($this->getFullAlias('labels'), function (ListTable $template) {
            return new Labels($template->name(), $template->config('labels', []), $template);
        })->withArgument($this->template);
    }

    /**
     * Déclaration du controleur de pagination.
     *
     * @return void
     */
    public function registerPagination()
    {
        $this->getContainer()->share($this->getFullAlias('pagination'), function ($attrs, ListTable $template) {
            return new Pagination($attrs, $template);
        })->withArguments([[], $this->template]);
    }

    /**
     * @inheritdoc
     */
    public function registerParams()
    {
        $this->getContainer()->share($this->getFullAlias('params'), function (ListTable $template) {
            return new Params($template->config('params', []), $template);
        })->withArgument($this->template);
    }

    /**
     * @inheritdoc
     */
    public function registerRequest()
    {
        $this->getContainer()->share($this->getFullAlias('request'), function (ListTable $template) {
            return (Request::capture())->setTemplate($template);
        })->withArgument($this->template);
    }

    /**
     * Déclaration des controleurs d'action sur une ligne d'élément.
     *
     * @return void
     */
    public function registerRowActions()
    {
        $this->getContainer()->share($this->getFullAlias('row-actions'), function (ListTable $template) {
            return new RowActionsCollection($template->param('row_actions', []), $template);
        })->withArgument($this->template);

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
        $this->getContainer()->share($this->getFullAlias('viewer'), function (ListTable $template) {
            $params = $template->config('viewer', []);

            if (!$params instanceof ViewEngine) :
                $viewer = new ViewEngine(
                    array_merge(
                        [
                            'directory' => template()->resourcesDir('/views/list-table')
                        ],
                        $params
                    )
                );
                $viewer->setController(Viewer::class);

                if (!$viewer->getOverrideDir()) :
                    $viewer->setOverrideDir(template()->resourcesDir('/views/list-table'));
                endif;
            else :
                $viewer = $params;
            endif;

            $viewer->set('template', $template);

            return $viewer;
        })->withArgument($this->template);
    }

    /**
     * Déclaration des controleurs de filtre de la vue.
     *
     * @return void
     */
    public function registerViewFilters()
    {
        $this->getContainer()->share($this->getFullAlias('view-filters'), function (ListTable $template) {
            return new ViewFiltersCollection($template->param('view_filters', []), $template);
        })->withArgument($this->template);

        $this->getContainer()->add($this->getFullAlias('view-filters.item'), ViewFiltersItem::class);
    }
}