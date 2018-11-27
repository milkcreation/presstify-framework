<?php

namespace tiFy\Layout\Share\ListTable\Item;

use ArrayIterator;
use Illuminate\Support\Collection;
use tiFy\Layout\Share\ListTable\Contracts\ItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\ItemCollectionInterface;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;

class ItemCollectionController implements ItemCollectionInterface
{
    /**
     * Liste des éléments.
     * @var void|ItemInterface[]
     */
    protected $items = [];

    /**
     * Instance de la disposition associée.
     * @var ListTableInterface
     */
    protected $layout;

    /**
     * Nombre total d'éléments.
     * @var int
     */
    protected $total = 0;

    /**
     * CONSTRUCTEUR.
     *
     * @param ListTableInterface $layout Instance de la disposition associée.
     *
     * @return void
     */
    public function __construct(ListTableInterface $layout)
    {
        $this->layout = $layout;

        $this->query($this->layout->request()->getQueryArgs());
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
    public function count()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * {@inheritdoc}
     */
    public function has()
    {
        return !empty($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) :
            $this->items[] = $value;
        else :
            $this->items[$key] = $value;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function query($query_args = [])
    {
        if ($db = $this->layout->db()) :
            $query = $db->query($query_args);

            if ($items = $query->getItems()) :
                foreach ($items as $item) :
                    $this->items[] = $this->layout->resolve('item', [$item, $this->layout]);
                endforeach;
            endif;

            $this->total = $query->getFoundItems();
        else :
            foreach ($this->layout->param('items', []) as $item) :
                $this->items[] = $this->layout->resolve('item', [$item, $this->layout]);
            endforeach;

            $this->total = count($this->items);
        endif;
    }
}