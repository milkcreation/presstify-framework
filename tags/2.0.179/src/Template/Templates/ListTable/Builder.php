<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
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
     * Mots clefs de recherche.
     * @var string
     */
    protected $search = '';

    /**
     * @inheritDoc
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * @inheritDoc
     */
    public function setItems(): BuilderContract
    {
        if ($this->db()) {
            $this->parse();

            $this->querySearch();
            $this->queryWhere();
            $this->queryOrder();
            $total = $this->query()->count();
            if ($total < $this->getPerPage()) {
                $this->setPage(1);
            }

            $this->queryLimit();
            $items = $this->query()->get();

            $this->factory->items()->set($items);

            if ($count = $items->count()) {
                $this->factory->pagination()
                    ->setCount($count)
                    ->setCurrentPage($this->getPage())
                    ->setPerPage($this->getPerPage())
                    ->setLastPage((int)ceil($total / $this->getPerPage()))
                    ->setTotal($total)
                    ->parse();
            }

            $this->resetQuery();
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

        $this->setSearch((string)$this->get('s', ''));

        if ($this->factory->ajax() && $this->pull('draw', 0)) {
            $this
                ->setSearch((string)$this->get('search.value', $this->getSearch()))
                ->setPerPage((int)$this->pull('length', $this->getPerPage()))
                ->setPage((int)ceil(($this->pull('start') / $this->getPerPage()) + 1));

            $this->pull('columns');
            $this->pull('search');
            $this->pull('action');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function querySearch(): EloquentBuilder
    {
        if ($terms = $this->getSearch()) {
            $this->query()->where($this->db()->getKeyName(), 'like', "%{$terms}%");
        }

        return $this->query();
    }

    /**
     * @inheritDoc
     */
    public function setSearch(string $search): BuilderContract
    {
        $this->search = $search;

        return $this;
    }
}