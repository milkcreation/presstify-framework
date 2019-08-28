<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

use tiFy\Contracts\Template\FactoryDb;
use tiFy\Support\Collection;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\ListTable\Contracts\{Items as ItemsContract, Item, ListTable};

class Items extends Collection implements ItemsContract
{
    use FactoryAwareTrait;

    /**
     * Indice de l'élément courant.
     * @var int
     */
    protected $index = 0;

    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * Nombre total d'éléments trouvés.
     * @var int
     */
    protected $total = 0;

    /**
     * Liste des éléments.
     * @var array|Item[]
     */
    protected $items = [];

    /**
     * Récupération du nombre total d'éléments trouvés.
     *
     * @return int
     */
    public function total(): int
    {
        return $this->count();
    }

    /**
     * @inheritDoc
     */
    public function walk($item, $key = null)
    {
        $object = null;
        if ($item instanceof FactoryDb) {
            $item = $item->attributesToArray();
        } elseif (is_object($item)) {
            $item = get_object_vars($item);
        }

        /** @var Item $item */
        $item = $this->factory->resolve('item')->set($item);

        $this->items[$key] = $item->setIndex($this->index++)->parse();
    }
}