<?php

namespace tiFy\View\Pattern\ListTable;

use tiFy\View\Pattern\ListTable\Contracts\ListTable as ListTableContract;
use tiFy\View\Pattern\ListTable\Contracts\Request;
use tiFy\View\ViewPatternController;

class ListTable extends ViewPatternController implements ListTableContract
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        ListTableServiceProvider::class
    ];

    /**
     * {@inheritdoc}
     */
    public function bulkActions()
    {
        return $this->resolve('bulk-actions');
    }

    /**
     * {@inheritdoc}
     */
    public function columns()
    {
        return $this->resolve('columns');
    }

    /**
     * {@inheritdoc}
     */
    public function item()
    {
        return $this->items()->current();
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        return $this->resolve('items');
    }

    /**
     * {@inheritdoc}
     */
    public function pagination()
    {
        return $this->resolve('pagination');
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->items()->query();

        if (!$this->items()->exists()) :
            return;
        endif;

        $total_items = $this->items()->getFounds();
        $per_page = $this->request()->getPerPage();
        $total_pages = ceil($total_items/$per_page);

        $this->pagination()
            ->set('per_page', $per_page)
            ->set('total_items', $total_items)
            ->set('total_pages', $total_pages);
    }

    /**
     * {@inheritdoc}
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
    public function request()
    {
        return parent::request();
    }

    /**
     * {@inheritdoc}
     */
    public function rowActions()
    {
        return $this->resolve('row-actions');
    }

    /**
     * {@inheritdoc}
     */
    public function viewFilters()
    {
        return $this->resolve('view-filters');
    }
}