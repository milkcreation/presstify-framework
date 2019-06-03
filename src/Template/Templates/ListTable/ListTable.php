<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Contracts\Template\{FactoryQueryBuilder, TemplateFactory as TemplateFactoryContract};
use tiFy\Template\TemplateFactory;
use tiFy\Template\Templates\ListTable\Contracts\{Ajax,
    BulkActions,
    Columns,
    Item,
    Items,
    ListTable as ListTableContract,
    Pagination,
    QueryBuilder,
    RowActions,
    Search,
    ViewFilters};

class ListTable extends TemplateFactory implements ListTableContract
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        ListTableServiceProvider::class
    ];

    /**
     * @inheritDoc
     */
    public function ajax(): ?Ajax
    {
        return $this->resolve('ajax');
    }

    /**
     * @inheritDoc
     */
    public function bulkActions(): BulkActions
    {
        return $this->resolve('bulk-actions');
    }

    /**
     * @inheritDoc
     */
    public function columns(): Columns
    {
        return $this->resolve('columns');
    }

    /**
     * @inheritDoc
     */
    public function item(): ?Item
    {
        return $this->items()->current();
    }

    /**
     * @inheritDoc
     */
    public function items(): Items
    {
        return $this->resolve('items');
    }

    /**
     * @inheritDoc
     */
    public function pagination(): Pagination
    {
        return $this->resolve('pagination');
    }

    /**
     * {@inheritDoc}
     *
     * @return ListTableContract
     */
    public function proceed(): TemplateFactoryContract
    {
        $this->items()->set($this->query()->proceed());

        if (!$this->items()->exists()) {
            return $this;
        }

        $this->pagination()->parse();

        if ($ajax = $this->ajax()) {
            $ajax->parse();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        return $this->viewer('list-table');
    }

    /**
     * {@inheritDoc}
     *
     * @return QueryBuilder
     */
    public function query(): FactoryQueryBuilder
    {
        return parent::query();
    }

    /**
     * @inheritDoc
     */
    public function rowActions(): RowActions
    {
        return $this->resolve('row-actions');
    }

    /**
     * @inheritDoc
     */
    public function search(): Search
    {
        return $this->resolve('search');
    }

    /**
     * @inheritDoc
     */
    public function viewFilters(): ViewFilters
    {
        return $this->resolve('view-filters');
    }
}