<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Contracts\Template\FactoryQueryBuilder as FactoryQueryBuilderContract;
use tiFy\Template\Templates\ListTable\Contracts\QueryBuilder as QueryBuilderContract;
use tiFy\Template\Factory\FactoryQueryBuilder;

class QueryBuilder extends FactoryQueryBuilder implements QueryBuilderContract
{
    /**
     * Instance du gabarit d'affichage.
     * @var ListTable
     */
    protected $factory;

    /**
     * {@inheritDoc}
     *
     * @return QueryBuilderContract
     */
    public function parse(): FactoryQueryBuilderContract
    {
        parent::parse();

        if ($this->factory->ajax() && $this->pull('draw', 0)) {
            $this->perPage = (int)$this->pull('length', $this->perPage);
            $this->pageNum = (int)ceil(($this->pull('start') / $this->perPage) + 1);

            $columns = $this->pull('columns');
            $search = $this->pull('search');
            $action = $this->pull('action');

            /*if ($this->searchExists()) {
                $query_args['search'] = $this->searchTerm();
            }
            if (isset($_REQUEST['order'])) {
                $query_args['orderby'] = [];
            }
            foreach ((array)$_REQUEST['order'] as $k => $v) {
                $query_args['orderby'][$_REQUEST['columns'][$v['column']]['data']] = $v['dir'];
            }*/
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function searchExists(): bool
    {
        return $this->factory->ajax() ? !empty($this->get('search.value')) : !empty($this->get('s'));
    }

    /**
     * @inheritDoc
     */
    public function searchTerm(): string
    {
        return $this->factory->ajax() ? $this->get('search.value', '') : $this->get('s', '');
    }
}