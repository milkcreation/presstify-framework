<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\Tab;

use Exception;
use Illuminate\Support\Collection;
use tiFy\Partial\Drivers\TabDriverInterface;

class TabCollection implements TabCollectionInterface
{
    /**
     * Indicateur de chargement.
     */
    private $booted = false;

    /**
     * Liste des éléments par groupe.
     * @var Collection[]|TabFactoryInterface[][]|array
     */
    private $grouped = [];

    /**
     * Valeur incrémentale de l'indice de qualification d'un élément.
     * @var int
     */
    private $itemIdx = 0;

    /**
     * Liste des éléments.
     * @var TabFactoryInterface[]
     */
    private $items = [];

    /**
     * Liste des éléments déclaré avant le chargement.
     * @var TabFactoryInterface[]|array
     */
    private $preBootItems = [];

    /**
     * Instance du gestionnaire.
     * @var TabDriverInterface|null
     */
    private $tabManager;

    /**
     * @param TabFactoryInterface[]|array|null $items Liste des éléments ou Liste d'attributs d'élements.
     */
    public function __construct($items = null)
    {
        foreach((array) $items as $item) {
            $this->add($item);
        }
    }

    /**
     * @inheritDoc
     */
    public function add($def): TabCollectionInterface
    {
        if ($this->isBooted()) {
            $this->preBootItems[] = $def;

            return $this;
        }

        if (!$def instanceof TabFactoryInterface) {
            if (!is_array($def)) {
                $def = [];
            }
            $item = (new TabFactory())->set($def);
        } else {
            $item = $def;
        }

        try {
            $this->items[] = $item->setCollection($this)->build();
        } catch(Exception $e) {
            unset($e);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function boot(): TabCollectionInterface
    {
        if (!$this->booted) {
            if (is_null($this->tabManager)) {
                throw new Exception('Unresolvable TabManager');
            }

            $this->booted = true;

            foreach($this->preBootItems as $def) {
                $this->add($def);
            }

            $this->walkRecursiveItems()->walkGrouped();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): ?TabFactoryInterface
    {
        return (new Collection($this->items))->first(function(TabFactoryInterface $item) use ($name) {
            return $item->getName() === $name;
        }) ? : null;
    }

    /**
     * @inheritDoc
     */
    public function getGrouped(string $parent = ''): iterable
    {
        return $this->grouped[$parent] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getIncreasedItemIdx(): int
    {
        return $this->itemIdx++;
    }

    /**
     * @inheritDoc
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * @inheritDoc
     */
    public function setTabManager(TabDriverInterface $tabManager): TabCollectionInterface
    {
        $this->tabManager = $tabManager;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function tabManager(): ?TabDriverInterface
    {
        return $this->tabManager;
    }

    /**
     * @inheritDoc
     */
    public function walkRecursiveItems(?array $items = null, int $depth = 0, string $parent = ''): TabCollectionInterface
    {
        if (is_null($items)) {
            $items = $this->items;
        }

        foreach ($items as $item) {
            if ($parent !== $item->getParentName()) {
                continue;
            } else {
                $item->setDepth($depth)->boot();
                $this->walkRecursiveItems($items, $depth + 1, $item->getName());
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function walkGrouped(?array $items = null): TabCollectionInterface
    {
        if (is_null($items)) {
            $items = $this->items;
        }
         $this->grouped = (new Collection($items))->groupBy('parent');

        foreach ($this->grouped as $key => $groupedItems) {
            $max = $groupedItems->max(function (TabFactoryInterface $item) {
                return $item['position'];
            }) ? : 0;
            $pad = 0;

            $this->grouped[$key] = $groupedItems->each(function (TabFactoryInterface $item) use (&$pad, $max) {
                $tab['position'] = is_numeric($item['position']) ? (int)$item['position'] : ++$pad + $max;
            })->sortBy('position')->values();
        }

        return $this;
    }
}