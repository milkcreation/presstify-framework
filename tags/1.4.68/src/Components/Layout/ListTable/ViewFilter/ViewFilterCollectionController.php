<?php

namespace tiFy\Components\Layout\ListTable\ViewFilter;

use Illuminate\Support\Collection;
use tiFy\Components\Layout\ListTable\ViewFilter\ViewFilterItemController;
use tiFy\Components\Layout\ListTable\ListTableInterface;

class ViewFilterCollectionController implements ViewFilterCollectionInterface
{
    /**
     * Classe de rappel de la vue associée.
     * @var ListTableInterface
     */
    protected $app;

    /**
     * Liste des filtres.
     * @var void|FilterItemController[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param ListTableInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct(ListTableInterface $app)
    {
        $this->app = $app;

        $this->parse($this->app->param('view_filters', []));
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

                $alias = $this->app->bound("view_filters.item.{$name}")
                    ? "view_filters.item.{$name}"
                    : ViewFilterItemInterface::class;

                $this->items[$name] = $this->app->resolve($alias, [$name, $attrs]);
            endforeach;

            $this->items = array_filter($this->items, function ($value) {
                return (string)$value !== '';
            });
        endif;
    }
}