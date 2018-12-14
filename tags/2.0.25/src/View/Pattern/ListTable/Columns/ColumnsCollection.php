<?php

namespace tiFy\View\Pattern\ListTable\Columns;

use Illuminate\Support\Collection;
use tiFy\View\Pattern\ListTable\Contracts\ColumnsCollection as ColumnsCollectionContract;
use tiFy\View\Pattern\ListTable\Contracts\ColumnsItem;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;

class ColumnsCollection implements ColumnsCollectionContract
{
    /**
     * Liste des colonnes.
     * @var Collection|ColumnsItem[]
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
     * @param array $items Liste des éléments
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
    public function all()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return $this->items[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders($with_id = true)
    {
        return $this->items->mapWithKeys(function(ColumnsItem $item, $key) use ($with_id){
            return [$key => $item->getHeader($with_id)];
        })->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getHidden()
    {
        return $this->items
            ->filter(function(ColumnsItem $item){
                return $item->isHidden();
            })
            ->pluck('name', null)
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getInfos()
    {
        $columns = $this->getList();
        $hidden = $this->getHidden();
        $sortable = $this->getSortable();
        $primary = $this->getPrimary();

        return [$columns, $hidden, $sortable, $primary];
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        return $this->items->pluck('title', 'name')->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimary()
    {
        if (!$this->getList()) :
            return '';
        elseif ($primary = $this->items->first(function (ColumnsItem $item) {return $item['primary'] === true;})) :
            return $primary->getName();
        else :
            return $this->items->first(function (ColumnsItem $item) {return $item['name'] !== 'cb';})->getName();
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortable()
    {
        return $this->items
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
        return $this->items
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
    public function isPrimary($name)
    {
        return $name === $this->getPrimary();
    }

    /**
     * {@inheritdoc}
     */
    public function parse($columns = [])
    {
        $_columns = [];
        foreach ($columns as $name => $attrs) :
            if (is_numeric($name)) :
                $name = $attrs;
                $attrs = [];
            elseif (is_string($attrs)) :
                $attrs = ['title' => $attrs];
            endif;

            $alias = $this->pattern->has("columns.item.{$name}")
                ? "columns.item.{$name}"
                : 'columns.item';

            $_columns[$name] = $this->pattern->get($alias, [$name, $attrs, $this->pattern]);
        endforeach;

        return $this->items = new Collection($_columns);
    }
}