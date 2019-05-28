<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Contracts\Template\{FactoryRequest, TemplateFactory as TemplateFactoryContract};
use tiFy\Template\TemplateFactory;
use tiFy\Template\Templates\ListTable\Contracts\{Ajax,
    BulkActionsCollection,
    Collection,
    ColumnsCollection,
    Item,
    ListTable as ListTableContract,
    Pagination,
    Request,
    RowActionsCollection,
    Search,
    ViewFiltersCollection};

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
     * @inheritdoc
     */
    public function ajax(): ?Ajax
    {
        return $this->resolve('ajax');
    }

    /**
     * @inheritdoc
     */
    public function bulkActions(): BulkActionsCollection
    {
        return $this->resolve('bulk-actions');
    }

    /**
     * @inheritdoc
     */
    public function columns(): ColumnsCollection
    {
        return $this->resolve('columns');
    }

    /**
     * @inheritdoc
     */
    public function item(): ?Item
    {
        return $this->items()->current();
    }

    /**
     * @inheritdoc
     */
    public function items(): Collection
    {
        return $this->resolve('items');
    }

    /**
     * @inheritdoc
     */
    public function pagination(): Pagination
    {
        return $this->resolve('pagination');
    }

    /**
     * {@inheritdoc}
     *
     * @return ListTableContract
     */
    public function prepare(): TemplateFactoryContract
    {
        if (!$this->prepared) {
            parent::prepare();

            $this->items()->query();

            if (!$this->items()->exists()) {
                return $this;
            }

            $total_items = $this->items()->total();
            $per_page = $this->request()->getPerPage();
            $total_pages = ceil($total_items / $per_page);

            $this->pagination()
                ->set('per_page', $per_page)
                ->set('total_items', $total_items)
                ->set('total_pages', $total_pages)
                ->parse();

            if ($ajax = $this->ajax()) {
                $ajax->parse()->all();
            }
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        return $this->viewer('list-table');
    }

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function request(): FactoryRequest
    {
        return parent::request();
    }

    /**
     * @inheritdoc
     */
    public function rowActions(): RowActionsCollection
    {
        return $this->resolve('row-actions');
    }

    /**
     * @inheritdoc
     */
    public function search(): Search
    {
        return $this->resolve('search');
    }

    /**
     * @inheritdoc
     */
    public function viewFilters(): ViewFiltersCollection
    {
        return $this->resolve('view-filters');
    }
}