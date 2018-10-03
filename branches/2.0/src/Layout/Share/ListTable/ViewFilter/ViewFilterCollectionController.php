<?php

namespace tiFy\Layout\Share\ListTable\ViewFilter;

use Illuminate\Support\Collection;
use tiFy\Layout\Share\ListTable\Contracts\ViewFilterCollectionInterface;
use tiFy\Layout\Share\ListTable\Contracts\ViewFilterItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;

class ViewFilterCollectionController implements ViewFilterCollectionInterface
{
    /**
     * Instance de la disposition associée.
     * @var ListTableInterface
     */
    protected $layout;

    /**
     * Liste des filtres.
     * @var void|ViewFilterItemInterface[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param ListTableInterface $layout Instance de la disposition associée.
     *
     * @return void
     */
    public function __construct(ListTableInterface $layout)
    {
        $this->layout = $layout;

        $this->parse($this->layout->param('view_filters', []));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
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

                $alias = $this->layout->bound("layout.view_filters.item.{$name}")
                    ? "view_filters.item.{$name}"
                    : 'view_filters.item';

                $this->items[$name] = $this->layout->resolve($alias, [$name, $attrs, $this->layout]);
            endforeach;

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        endif;
    }
}