<?php

namespace tiFy\View\Pattern\ListTable\Items;

use tiFy\Kernel\Collection\QueryCollection;
use tiFy\View\Pattern\ListTable\Contracts\Collection as CollectionContract;
use tiFy\View\Pattern\ListTable\Contracts\Item;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;

class Collection extends QueryCollection implements CollectionContract
{
    /**
     * Liste des éléments.
     * @var array|Item[]
     */
    protected $items = [];

    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items Liste des éléments.
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($items, ListTable $pattern)
    {
        $this->pattern = $pattern;

        foreach ($items as $item) :
            $this->items[] = $this->wrap($item);
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function query($args = [])
    {
        if ($db = $this->pattern->db()) :
            $this->items = [];

            $query = $db->query(
                array_merge(
                    $this->pattern->request()->getQueryArgs(),
                    $args
                )
            );

            if ($items = $query->getItems()) :
                foreach ($items as $item) :
                    $this->items[] = $this->wrap($item);
                endforeach;
            endif;

            $this->setFounds($query->getFoundItems());
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function wrap($item)
    {
        return $this->pattern->get('item', [$item, $this->pattern]);
    }
}