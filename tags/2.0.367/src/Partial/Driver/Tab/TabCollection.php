<?php declare(strict_types=1);

namespace tiFy\Partial\Driver\Tab;

use Exception;
use Illuminate\Support\Collection;
use tiFy\Contracts\Partial\Tab as TabManager;
use tiFy\Contracts\Partial\TabCollection as TabCollectionContract;
use tiFy\Contracts\Partial\TabFactory as TabFactoryContract;

class TabCollection implements TabCollectionContract
{
    /**
     * Indicateur de chargement.
     */
    private $booted = false;

    /**
     * Liste des éléments par groupe.
     * @var Collection[]|TabFactoryContract[][]|array
     */
    private $grouped = [];

    /**
     * Valeur incrémentale de l'indice de qualification d'un élément.
     * @var int
     */
    private $itemIdx = 0;

    /**
     * Liste des éléments.
     * @var TabFactoryContract[]
     */
    private $items = [];

    /**
     * Liste des éléments déclaré avant le chargement.
     * @var TabFactoryContract[]|array
     */
    private $preBootItems = [];

    /**
     * Instance du gestionnaire.
     * @var TabManager|null
     */
    private $tabManager;

    /**
     * @param TabFactoryContract[]|array|null $items Liste des éléments ou Liste d'attributs d'élements.
     *
     * @return void
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
    public function add($def): TabCollectionContract
    {
        if ($this->isBooted()) {
            $this->preBootItems[] = $def;

            return $this;
        }

        if (!$def instanceof TabFactoryContract) {
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
    public function boot(): TabCollectionContract
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
    public function get(string $name): ?TabFactoryContract
    {
        return (new Collection($this->items))->first(function(TabFactoryContract $item) use ($name) {
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
    public function setTabManager(TabManager $tabManager): TabCollectionContract
    {
        $this->tabManager = $tabManager;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function tabManager(): ?TabManager
    {
        return $this->tabManager;
    }

    /**
     * @inheritDoc
     */
    public function walkRecursiveItems(?array $items = null, int $depth = 0, string $parent = ''): TabCollectionContract
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
    public function walkGrouped(?array $items = null): TabCollectionContract
    {
        if (is_null($items)) {
            $items = $this->items;
        }
         $this->grouped = (new Collection($items))->groupBy('parent');

        foreach ($this->grouped as $key => $groupedItems) {
            $max = $groupedItems->max(function (TabFactoryContract $item) {
                return $item['position'];
            }) ? : 0;
            $pad = 0;

            $this->grouped[$key] = $groupedItems->each(function (TabFactoryContract $item) use (&$pad, $max) {
                $tab['position'] = is_numeric($item['position']) ? (int)$item['position'] : ++$pad + $max;
            })->sortBy('position')->values();
        }

        return $this;
    }
}