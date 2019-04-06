<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Columns;

use tiFy\Support\Collection;
use tiFy\Template\Templates\ListTable\Contracts\ColumnsCollection as ColumnsCollectionContract;
use tiFy\Template\Templates\ListTable\Contracts\ColumnsItem;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class ColumnsCollection extends Collection implements ColumnsCollectionContract
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Liste des colonnes.
     * @var ColumnsItem[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items Liste des éléments
     * @param ListTable $factory Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(array $items, ListTable $factory)
    {
        $this->factory = $factory;

        $this->parse($items);
    }

    /**
     * @inheritdoc
     */
    public function countVisible(): int
    {
        return count($this->getVisible());
    }

    /**
     * @inheritdoc
     */
    public function getHidden(): array
    {
        return $this->collect()
            ->filter(function (ColumnsItem $item) {
                return $item->isHidden();
            })
            ->pluck('name', null)
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function getPrimary(): string
    {
        if (
            ($column_primary = $this->factory->param('column_primary', '')) &&
            ($column_primary !== 'cb') &&
            $this->has($column_primary)
        ) {
            return (string)$column_primary;
        } else {
            return $this->collect()->first(function (ColumnsItem $item) {
                return $item->getName() !== 'cb';
            })->getName();
        }
    }

    /**
     * @inheritdoc
     */
    public function getSortable(): array
    {
        return $this->collect()
            ->filter(function (ColumnsItem $item) {
                return $item->isSortable();
            })
            ->pluck('sortable', 'name')
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function getVisible(): array
    {
        return $this->collect()
            ->filter(function (ColumnsItem $item) {
                return !$item->isHidden();
            })
            ->pluck('name', null)
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function parse(array $columns = []): ColumnsCollectionContract
    {
        foreach ($columns as $name => $attrs) {
            if (is_numeric($name)) {
                $name = $attrs;
                $attrs = [];
            } elseif (is_string($attrs)) {
                $attrs = ['title' => $attrs];
            }

            $alias = $this->factory->bound("columns.item.{$name}")
                ? "columns.item.{$name}"
                : 'columns.item';

            $this->items[$name] = $this->factory->resolve($alias, [$name, $attrs, $this->factory]);
        }

        return $this;
    }
}