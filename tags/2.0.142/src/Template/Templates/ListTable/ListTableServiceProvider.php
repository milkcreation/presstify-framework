<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Template\Factory\FactoryServiceProvider;
use tiFy\Template\Templates\ListTable\Contracts\{
    Ajax as AjaxContract,
    BulkAction,
    BulkActions,
    Column,
    Columns,
    HttpXhrController,
    ListTable,
    Item,
    Items,
    Pagination,
    Params,
    QueryBuilder,
    Search,
    RowAction,
    RowActions,
    ViewFilter,
    ViewFilters};
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
            $ajax = $this->factory->get('providers.ajax');
            $ajax = $ajax instanceof AjaxContract
                ? $ajax
                : $this->getContainer()->get(AjaxContract::class);

            $attrs = $this->factory->param('ajax', []);
            if (is_string($attrs)) {
                $attrs = [
                    'url'      => $attrs,
                    'dataType' => 'json',
                    'type'     => 'POST'
                ];
            }

            return $ajax->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : []);
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
            $ctrl = $this->factory->get('providers.bulk-actions');
            $ctrl = $ctrl instanceof BulkActions
                ? $ctrl
                : $this->getContainer()->get(BulkActions::class);

            $attrs = $this->factory->param('bulk_actions', []);

            return $ctrl->setTemplateFactory($this->factory)->parse(is_array($attrs) ? $attrs : []);
        });

        $this->getContainer()->add($this->getFactoryAlias('bulk-action'), function (string $name, array $attrs = []) {
            $ctrl = $this->factory->get('providers.bulk-action');
            $ctrl = $ctrl instanceof BulkAction
                ? $ctrl
                : $this->getContainer()->get(BulkAction::class, [$name, $attrs]);

            return $ctrl->setTemplateFactory($this->factory);
        });

        $this->getContainer()->add($this->getFactoryAlias('bulk-action.trash'),
            function (string $name, array $attrs = []) {
                return (new BulkActionTrash($name, $attrs));
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
            $ctrl = $this->factory->get('providers.columns');
            $ctrl = $ctrl instanceof Columns
                ? $ctrl
                : $this->getContainer()->get(Columns::class);

            $attrs = $this->factory->param('columns', []);

            return $ctrl->setTemplateFactory($this->factory)->parse(is_array($attrs) ? $attrs : []);
        });

        $this->getContainer()->add($this->getFactoryAlias('column'), function (string $name, array $attrs = []) {
            $ctrl = $this->factory->get('providers.column');
            $ctrl = $ctrl instanceof Column
                ? $ctrl
                : $this->getContainer()->get(Column::class);

            return $ctrl->setTemplateFactory($this->factory)->setName($name)->set($attrs)->parse();
        });

        $this->getContainer()->add($this->getFactoryAlias('column.cb'),
            function (string $name, array $attrs = []) {
                return (new ColumnCb())->setName($name)->set($attrs)->parse();
            });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryHttpXhrController(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('xhr'), function () {
            $ctrl = $this->factory->get('providers.xhr');
            $ctrl = $ctrl instanceof HttpXhrController
                ? $ctrl
                : $this->getContainer()->get(HttpXhrController::class);

            return $ctrl->setTemplateFactory($this->factory);
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
            $ctrl = $this->factory->get('providers.items');

            $ctrl = $ctrl instanceof Items
                ? $ctrl
                : $this->getContainer()->get(Items::class);

            return $ctrl->setTemplateFactory($this->factory)->set($this->factory->get('items', []));
        });

        $this->getContainer()->add($this->getFactoryAlias('item'), function () {
            $ctrl = $this->factory->get('providers.item');
            $ctrl = $ctrl instanceof Item
                ? $ctrl
                : $this->getContainer()->get(Item::class);

            return $ctrl->setTemplateFactory($this->factory);
        });
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
            $ctrl = $this->factory->get('providers.pagination');
            $ctrl = $ctrl instanceof Pagination
                ? $ctrl
                : $this->getContainer()->get(Pagination::class);

            return $ctrl->setTemplateFactory($this->factory);
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryParams(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('params'), function () {
            $ctrl = $this->factory->get('providers.params');
            $ctrl = $ctrl instanceof Params
                ? $ctrl
                : $this->getContainer()->get(Params::class);

            $attrs = $this->factory->get('params', []);

            return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : [])->parse();
        });
    }

    /**
     * Déclaration du controleur de construction de requête.
     *
     * @return void
     */
    public function registerFactoryQueryBuilder(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('query-builder'), function () {
            $ctrl = $this->factory->get('providers.query-builder');
            $ctrl = $ctrl instanceof QueryBuilder
                ? $ctrl
                : $this->getContainer()->get(QueryBuilder::class);

            $attrs = $this->factory->param('query_args', []);

            return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : []);
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
            $ctrl = $this->factory->get('providers.row-actions');
            $ctrl = $ctrl instanceof RowActions
                ? $ctrl
                : $this->getContainer()->get(RowActions::class);

            $attrs = $this->factory->param('row_actions', []);

            return $ctrl->setTemplateFactory($this->factory)->parse(is_array($attrs) ? $attrs : []);
        });

        $this->getContainer()->share($this->getFactoryAlias('row-action'), function (string $name, array $attrs = []) {
            $ctrl = $this->factory->get('providers.row-action');
            $ctrl = $ctrl instanceof RowAction
                ? $ctrl
                : $this->getContainer()->get(RowAction::class);

            return $ctrl->setTemplateFactory($this->factory)->setName($name)->set($attrs)->parse();
        });

        $this->getContainer()->add($this->getFactoryAlias('row-action.activate'),
            function (string $name, array $attrs = []) {
                return (new RowActionActivate())->setName($name)->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('row-action.deactivate'),
            function (string $name, array $attrs = []) {
                return (new RowActionDeactivate())->setName($name)->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('row-action.delete'),
            function (string $name, array $attrs = []) {
                return (new RowActionDelete())->setName($name)->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('row-action.duplicate'),
            function (string $name, array $attrs = []) {
                return (new RowActionDuplicate())->setName($name)->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('row-action.edit'),
            function (string $name, array $attrs = []) {
                return (new RowActionEdit())->setName($name)->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('row-action.preview'),
            function (string $name, array $attrs = []) {
                return (new RowActionPreview())->setName($name)->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('row-action.trash'),
            function (string $name, array $attrs = []) {
                return (new RowActionTrash())->setName($name)->set($attrs)->parse();
            });

        $this->getContainer()->add($this->getFactoryAlias('row-action.untrash'),
            function (string $name, array $attrs = []) {
                return (new RowActionUntrash())->setName($name)->set($attrs)->parse();
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
            $ctrl = $this->factory->get('providers.search');
            $ctrl = $ctrl instanceof Search
                ? $ctrl
                : $this->getContainer()->get(Search::class);

            $attrs = $this->factory->param('search', []);

            return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : [])->parse();
        });
    }

    /**
     * @inheritDoc
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
            $ctrl = $this->factory->get('providers.view-filters');
            $ctrl = $ctrl instanceof ViewFilters
                ? $ctrl
                : $this->getContainer()->get(ViewFilters::class);

            $attrs = $this->factory->param('view_filters', []);

            return $ctrl->setTemplateFactory($this->factory)->parse(is_array($attrs) ? $attrs : []);
        });

        $this->getContainer()->share($this->getFactoryAlias('view-filter'), function (string $name, array $attrs = []) {
            $ctrl = $this->factory->get('providers.view-filter');
            $ctrl = $ctrl instanceof ViewFilter
                ? $ctrl
                : $this->getContainer()->get(ViewFilter::class);

            return $ctrl->setTemplateFactory($this->factory)->setName($name)->set($attrs)->parse();
        });
    }
}