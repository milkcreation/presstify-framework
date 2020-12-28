<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Dropdown;

use tiFy\Partial\Drivers\DropdownDriverInterface;
use tiFy\Support\Collection;

class DropdownCollection extends Collection implements DropdownCollectionInterface
{
    /**
     * Instance du controleur d'affichage associé.
     * @var DropdownDriverInterface
     */
    protected $partial;

    /**
     * Liste des éléments.
     * @var DropdownItem[]|array
     */
    protected $items = [];

    /**
     * @param array $items Liste des éléments.
     */
    public function __construct(array $items)
    {
        array_walk($items, [$this, 'walk']);
    }

    /**
     * @inheritDoc
     */
    public function setPartial(DropdownDriverInterface $partial): DropdownCollectionInterface
    {
        $this->partial = $partial;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function walk($item, $key = null): DropdownItemInterface
    {
        if(!$item instanceof DropdownItemInterface) {
            $item = new DropdownItem($key, $item);
        }

        return $this->items[$key] = $item;
    }
}