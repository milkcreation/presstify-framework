<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Template\Factory\FactoryServiceProvider;
use tiFy\Template\Templates\ListTable\Ajax\Ajax;
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
use tiFy\Template\Templates\ListTable\Search\Search;
use tiFy\Template\Templates\ListTable\Viewer\Viewer;
use tiFy\Template\Templates\ListTable\ViewFilters\ViewFiltersCollection;
use tiFy\Template\Templates\ListTable\ViewFilters\ViewFiltersItem;
use tiFy\View\ViewEngine;

class ListTableServiceProvider extends FactoryServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var ListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function registerFactories(): void
    {
        parent::registerFactories();

        $this->registerFactoryAjax();
        $this->registerFactoryBulkActions();
        $this->registerFactoryColumns();
        $this->registerFactoryItems();
        $this->registerFactoryPagination();
        $this->registerFactoryRowActions();
        $this->registerFactorySearch();
        $this->registerFactoryViewFilters();
    }

    /**
     * Déclaration du controleurs de gestion de la table en ajax.
     *
     * @return void
     */
    public function registerFactoryAjax(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('ajax'), function () {
            return $this->factory->param('ajax') ? new Ajax($this->factory) : null;
        });
    }

    /**
     * Déclaration des controleurs d'actions groupées.
     *
     * @return void
     */
    public function registerFactoryBulkActions(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('bulk-actions'), function () {
            return new BulkActionsCollection($this->factory);
        });

        $this->getContainer()->add(
            $this->getFactoryAlias('bulk-actions.item'),
            function (string $name, array $attrs, ListTable $factory) {
                return new BulkActionsItem($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('bulk-actions.item.trash'),
            function (string $name, array $attrs, ListTable $factory) {
                return new BulkActionsItemTrash($name, $attrs, $factory);
            });
    }

    /**
     * Déclaration des controleurs de colonnes de la table.
     *
     * @return void
     */
    public function registerFactoryColumns(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('columns'), function () {
            if (!$columns = $this->factory->param('columns', [])) {
                $columns = ($this->getContainer()->has($this->getFactoryAlias('db')))
                    ? $this->getContainer()->get($this->getFactoryAlias('db'))->getColNames() : [];
            }
            return new ColumnsCollection($columns, $this->factory);
        });

        $this->getContainer()->add(
            $this->getFactoryAlias('columns.item'), function ($name, $attrs, ListTable $factory) {
            return new ColumnsItem($name, $attrs, $factory);
        });

        $this->getContainer()->add(
            $this->getFactoryAlias('columns.item.cb'),
            function ($name, $attrs, ListTable $factory) {
                return new ColumnsItemCb($name, $attrs, $factory);
            });
    }

    /**
     * Déclaration des controleurs d'éléments.
     *
     * @return void
     */
    public function registerFactoryItems(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('items'), function () {
            return new Collection($this->factory);
        });

        $this->getContainer()->add($this->getFactoryAlias('item'), Item::class);
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryLabels(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('labels'), function () {
            return new Labels($this->factory);
        });
    }

    /**
     * Déclaration du controleur de pagination.
     *
     * @return void
     */
    public function registerFactoryPagination(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('pagination'), function () {
            return new Pagination($this->factory);
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryParams(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('params'), function () {
            return new Params($this->factory);
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryRequest(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('request'), function () {
            return (Request::capture())->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration des controleurs d'action sur une ligne d'élément.
     *
     * @return void
     */
    public function registerFactoryRowActions(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('row-actions'), function () {
            return new RowActionsCollection($this->factory);
        });

        $this->getContainer()->add($this->getFactoryAlias('row-actions.item'),
            function ($name, $attrs, ListTable $factory) {
                new RowActionsItem($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('row-actions.item.activate'),
            function ($name, $attrs, ListTable $factory) {
                new RowActionsItemActivate($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('row-actions.item.deactivate'),
            function ($name, $attrs, ListTable $factory) {
                new RowActionsItemDeactivate($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('row-actions.item.delete'),
            function ($name, $attrs, ListTable $factory) {
                new RowActionsItemDelete($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('row-actions.item.duplicate'),
            function ($name, $attrs, ListTable $factory) {
                new RowActionsItemDuplicate($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('row-actions.item.edit'),
            function ($name, $attrs, ListTable $factory) {
                new RowActionsItemEdit($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('row-actions.item.preview'),
            function ($name, $attrs, ListTable $factory) {
                new RowActionsItemPreview($name, $attrs, $factory);
            });


        $this->getContainer()->add(
            $this->getFactoryAlias('row-actions.item.trash'),
            function ($name, $attrs, ListTable $factory) {
                new RowActionsItemTrash($name, $attrs, $factory);
            });

        $this->getContainer()->add(
            $this->getFactoryAlias('row-actions.item.untrash'),
            function ($name, $attrs, ListTable $factory) {
                new RowActionsItemUntrash($name, $attrs, $factory);
            });
    }

    /**
     * Déclaration du controleurs de gestion du formulaire de recherche.
     *
     * @return void
     */
    public function registerFactorySearch(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('search'), function () {
            return new Search($this->factory);
        });
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerFactoryViewer(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('viewer'), function () {
            $params = $this->factory->get('viewer', []);

            if (!$params instanceof ViewEngine) {
                $viewer = new ViewEngine(array_merge([
                    'directory' => template()->resourcesDir('/views/list-table')
                ], $params));
                $viewer->setController(Viewer::class);

                if (!$viewer->getOverrideDir()) {
                    $viewer->setOverrideDir(template()->resourcesDir('/views/list-table'));
                }
            } else {
                $viewer = $params;
            }

            $viewer->set('factory', $this->factory);

            return $viewer;
        });
    }

    /**
     * Déclaration des controleurs de filtres de la vue.
     *
     * @return void
     */
    public function registerFactoryViewFilters(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('view-filters'), function () {
            return new ViewFiltersCollection($this->factory);
        });

        $this->getContainer()->add(
            $this->getFactoryAlias('view-filters.item'),
            function ($name, $attrs, ListTable $factory) {
                new ViewFiltersItem($name, $attrs, $factory);
            });
    }
}