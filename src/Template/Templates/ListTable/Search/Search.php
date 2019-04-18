<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Search;

use tiFy\Support\ParamsBag;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use tiFy\Template\Templates\ListTable\Contracts\Search as SearchContract;

class Search extends ParamsBag implements SearchContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * CONSTRUCTEUR.
     *
     * @param ListTable $factory Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(ListTable $factory)
    {
        $this->factory = $factory;

        $attrs = $this->factory->param('search', []);

        $this->set(is_array($attrs) ? $attrs : [])->parse();
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritdoc
     */
    public function defaults(): array
    {
        return [
            'attrs' => []
        ];
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function render(): string
    {
        return (string)$this->factory->viewer('search');
    }
}