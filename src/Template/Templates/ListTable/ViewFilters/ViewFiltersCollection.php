<?php

namespace tiFy\Template\Templates\ListTable\ViewFilters;

use tiFy\Kernel\Collection\Collection;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use tiFy\Template\Templates\ListTable\Contracts\ViewFiltersItem;
use tiFy\Template\Templates\ListTable\Contracts\ViewFiltersCollection as ViewFiltersCollectionContract;

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
    protected $template;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items Liste des éléments.
     * @param ListTable $template Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($items, ListTable $template)
    {
        $this->template = $template;

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

                $alias = $this->template->bound("view-filters.item.{$name}")
                    ? "view-filters.item.{$name}"
                    : 'view-filters.item';

                $this->items[$name] = $this->template->resolve($alias, [$name, $attrs, $this->template]);
            endforeach;

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        endif;
    }
}