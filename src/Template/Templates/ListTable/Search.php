<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Support\ParamsBag;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Contracts\{ListTable, Search as SearchContract};

class Search extends ParamsBag implements SearchContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'attrs' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(): SearchContract
    {
        parent::parse();

        if ($this->factory->ajax()) {
            $this->set('attrs.data-control', 'list-table.search');
        }

        $this->set('attrs.class', 'search');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return (string)$this->factory->viewer('search');
    }
}