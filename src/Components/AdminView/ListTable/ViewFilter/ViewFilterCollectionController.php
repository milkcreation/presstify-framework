<?php

namespace tiFy\Components\AdminView\ListTable\ViewFilter;

use Illuminate\Support\Collection;
use tiFy\Components\AdminView\ListTable\ViewFilter\ViewFilterItemController;
use tiFy\Components\AdminView\ListTable\ListTableInterface;

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
        foreach ($filters as $name => $attrs) :
            if (is_numeric($name)) :
                $name = $attrs;
                $attrs = [];
            elseif (is_string($attrs)) :
                $attrs = ['content' => $attrs];
            endif;

            $provide = $this->app->provider()->has("view_filters.item.{$name}")
                ? "view_filters.item.{$name}"
                : 'view_filters.item';

            $this->items[$name] = $this->app->provide($provide, [$name, $attrs]);
        endforeach;

        $this->items = array_filter($this->items, function ($value) {
            return (string)$value !== '';
        });
    }
}