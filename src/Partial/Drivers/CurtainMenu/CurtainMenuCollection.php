<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\CurtainMenu;

use tiFy\Partial\Drivers\CurtainMenuDriverInterface;
use tiFy\Support\Collection;

class CurtainMenuCollection extends Collection implements CurtainMenuCollectionInterface
{
    /**
     * Liste des instances des éléments associés.
     * @var CurtainMenuItemInterface[]
     */
    protected $items = [];

    /**
     * Instance du controleur d'affichage.
     * @var CurtainMenuDriverInterface
     */
    protected $partial;

    /**
     * Nom de qualification de l'élément sélectionné.
     * @var string|null
     */
    protected $selected;

    /**
     * @param mixed $items Liste des éléments.
     * @param string|null $selected Nom de qualification de l'élément séléctionné.
     */
    public function __construct($items, ?string $selected = null)
    {
        $this->set($items);
        $this->selected = $selected;
    }

    /**
     * @inheritDoc
     */
    public function getParentItems(?string $parent = null): array
    {
        return $this->collect()->where('parent', $parent)->all();
    }

    /**
     * @inheritDoc
     */
    public function prepare(CurtainMenuDriverInterface $partial): CurtainMenuCollectionInterface
    {
        $this->partial = $partial;

        $this->prepareItems($this->items);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param CurtainMenuItemInterface[]|array $items Liste des éléments à traiter.
     */
    public function prepareItems(array $items = [], int $depth = 0, ?string $parent = null): CurtainMenuCollectionInterface
    {
        foreach ($items as $item) {
            if ($item->getParentName() === $parent) {
                $item->parse()->setDepth($depth+1);
                $this->prepareItems($items, ($depth + 1), $item->getName());
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function walk($item, $key = null): void
    {
        if (!$item instanceof CurtainMenuItem) {
            $item = new CurtainMenuItem((string)$key, (array)$item);
        }

        $this->items[(string)$key] = $item->setManager($this);
    }
}