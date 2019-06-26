<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Contracts\Template\FactoryBuilder as FactoryBuilderContract;
use tiFy\Template\Factory\FactoryBuilder;
use tiFy\Template\Templates\ListTable\Contracts\Builder as BuilderContract;

class Builder extends FactoryBuilder implements BuilderContract
{
    /**
     * Instance du gabarit d'affichage.
     * @var ListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function setItems(): BuilderContract
    {
        if ($this->db()) {
            $this->parse();

            $this->queryWhere();
            $this->queryOrder();
            $this->queryLimit();
            $items = $this->query()->get();
            $count = $items->count();
            $this->resetQuery();

            $this->factory->items()->set($items);

            if ($count) {
                $total = $this->queryWhere()->count();
                $this->resetQuery();

                $this->factory->pagination()->set([
                    'current_page' => $this->pageNum,
                    'count'        => $count,
                    'last_page'    => ceil($total / $this->perPage),
                    'total'        => $total,
                ])->parse();
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return BuilderContract
     */
    public function parse(): FactoryBuilderContract
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