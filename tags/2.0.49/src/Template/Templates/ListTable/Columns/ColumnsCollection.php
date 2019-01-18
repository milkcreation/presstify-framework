<?php

namespace tiFy\Template\Templates\ListTable\Columns;

use tiFy\Kernel\Collection\Collection;
use tiFy\Template\Templates\ListTable\Contracts\ColumnsCollection as ColumnsCollectionContract;
use tiFy\Template\Templates\ListTable\Contracts\ColumnsItem;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;
use League\Container\Argument\RawArgument;

class ColumnsCollection extends Collection implements ColumnsCollectionContract
{
    /**
     * Liste des colonnes.
     * @var ColumnsItem[]
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
     * @param array $items Liste des éléments
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
    public function getHidden()
    {
        return $this->collect()
            ->filter(function(ColumnsItem $item){
                return $item->isHidden();
            })
            ->pluck('name', null)
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimary()
    {
        if (
            ($column_primary = $this->template->param('column_primary')) &&
            ($column_primary !== 'cb') &&
            $this->has($column_primary)
        ) :
            return $column_primary;
        else :
            return $this->collect()->first(function (ColumnsItem $item) {
                return $item->getName() !== 'cb';
            })->getName();
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortable()
    {
        return $this->collect()
            ->filter(function(ColumnsItem $item){
                return $item->isSortable();
            })
            ->pluck('sortable', 'name')
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getVisible()
    {
        return $this->collect()
            ->filter(function(ColumnsItem $item){
                return !$item->isHidden();
            })
            ->pluck('name', null)
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function countVisible()
    {
        return count($this->getVisible());
    }

    /**
     * {@inheritdoc}
     */
    public function parse($columns = [])
    {
        foreach ($columns as $name => $attrs) :
            if (is_numeric($name)) :
                $name = $attrs;
                $attrs = [];
            elseif (is_string($attrs)) :
                $attrs = ['title' => $attrs];
            endif;

            $alias = $this->template->bound("columns.item.{$name}")
                ? "columns.item.{$name}"
                : 'columns.item';

            $this->items[$name] = $this->template->resolve($alias, [new RawArgument($name), $attrs, $this->template]);
        endforeach;
    }
}