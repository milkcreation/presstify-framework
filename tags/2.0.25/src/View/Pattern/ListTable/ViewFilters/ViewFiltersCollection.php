<?php

namespace tiFy\View\Pattern\ListTable\ViewFilters;

use tiFy\Kernel\Collection\Collection;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;
use tiFy\View\Pattern\ListTable\Contracts\ViewFiltersItem;
use tiFy\View\Pattern\ListTable\Contracts\ViewFiltersCollection as ViewFiltersCollectionContract;

class ViewFiltersCollection extends Collection implements ViewFiltersCollectionContract
{
    /**
     * Liste des filtres.
     * @var array|ViewFiltersItem[]
     */
    protected $items = [];

    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items Liste des éléments.
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($items, ListTable $pattern)
    {
        $this->pattern = $pattern;

        $this->parse($items);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($filters = [])
    {
        if ($filters) :
            foreach ($filters as $name => $attrs) :
                if (is_numeric($name)) :
                    $name = $attrs;
                    $attrs = [];
                elseif (is_string($attrs)) :
                    $attrs = ['content' => $attrs];
                endif;

                $alias = $this->pattern->has("view-filters.item.{$name}")
                    ? "view-filters.item.{$name}"
                    : 'view-filters.item';

                $this->items[$name] = $this->pattern->get($alias, [$name, $attrs, $this->pattern]);
            endforeach;

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        endif;
    }
}