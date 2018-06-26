<?php

namespace tiFy\Components\Layout\ListTable\Item;

use ArrayIterator;
use Illuminate\Support\Collection;
use tiFy\Components\Layout\ListTable\Item\ItemController;
use tiFy\Components\Layout\ListTable\ListTableInterface;

class ItemCollectionController implements ItemCollectionInterface
{
    /**
     * Classe de rappel de la vue associée.
     * @var ListTableInterface
     */
    protected $app;

    /**
     * Liste des éléments.
     * @var void|ItemController[]
     */
    protected $items = [];

    /**
     * Nombre total d'éléments.
     * @var int
     */
    protected $total = 0;

    /**
     * CONSTRUCTEUR.
     *
     * @param ListTableInterface $app Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct(ListTableInterface $app)
    {
        $this->app = $app;

        $this->query($this->app->getQueryArgs());
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
     * Récupération de l'itérateur.
     *
     * @return ArrayIterator
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
     * Vérifie l'existance d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Récupération de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    /**
     * Définition de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     * @param mixed $value Valeur à définir.
     *
     * @return void
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
     * Suppression de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return void
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
        if (!$db = $this->app->getDb()) :
            return;
        endif;

        $query = $db->query($query_args);

        if ($items = $query->getItems()) :
            foreach($items as $item) :
                $this->items[] = new ItemController($item, $this->app);
            endforeach;
        endif;

        $this->total = $query->getFoundItems();
    }
}