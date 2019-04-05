<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Items;

use tiFy\Support\Collection as tiFyCollection;
use tiFy\Template\Templates\ListTable\Contracts\Collection as CollectionContract;
use tiFy\Template\Templates\ListTable\Contracts\Item;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

class Collection extends tiFyCollection implements CollectionContract
{
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
     * CONSTRUCTEUR.
     *
     * @param ListTable $factory Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(ListTable $factory)
    {
        $this->factory = $factory;

        $items = $this->factory->config('items', []);

        array_walk($items, [$this, 'walk']);

        $this->total = count($items);
    }

    /**
     * Récupération du nombre total d'éléments trouvés.
     *
     * @return int
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * @inheritdoc
     */
    public function query(array $args = []): CollectionContract
    {
        if ($db = $this->factory->db()) {
            $this->items = [];

            $query = $db->query_loop(array_merge($this->factory->request()->getQueryArgs(), $args));

            if ($items = $query->getItems()) {
                array_walk($items, [$this, 'walk']);
            }

            $this->total = $query->getFoundItems();
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function walk($item, $key = null)
    {
        $this->items[$key] = $this->factory->resolve('item', [$item, $this->factory]);
    }
}