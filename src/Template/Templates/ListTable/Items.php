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
            $object = $item;
            $item = $item->attributesToArray();
        } elseif (is_object($item)) {
            $object = $item;
            $item = get_object_vars($item);
        }

        /** @var Item $item */
        $item = $this->factory->resolve('item')->set($item);

        if (!is_null($object)) {
            $item->setObject($object);
        }

        $this->items[$key] = $item->parse();
    }
}