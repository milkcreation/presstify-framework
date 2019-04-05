<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\ViewFilters;

use tiFy\Support\Collection;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use tiFy\Template\Templates\ListTable\Contracts\ViewFiltersCollection as ViewFiltersCollectionContract;
use tiFy\Template\Templates\ListTable\Contracts\ViewFiltersItem;

class ViewFiltersCollection extends Collection implements ViewFiltersCollectionContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Liste des filtres.
     * @var array|ViewFiltersItem[]
     */
    protected $items = [];

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

        $attrs = $this->factory->param('view_filters', []);

        $this->parse(is_array($attrs) ? $attrs : []);
    }

    /**
     * @inheritdoc
     */
    public function parse(array $filters = []): ViewFiltersCollectionContract
    {
        if ($filters) {
            foreach ($filters as $name => $attrs) {
                if (is_numeric($name)) {
                    $name = $attrs;
                    $attrs = [];
                } elseif (is_string($attrs)) {
                    $attrs = ['content' => $attrs];
                }

                $alias = $this->factory->bound("view-filters.item.{$name}")
                    ? "view-filters.item.{$name}"
                    : 'view-filters.item';

                $this->items[$name] = $this->factory->resolve($alias, [$name, $attrs, $this->factory]);
            }

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        }

        return $this;
    }
}