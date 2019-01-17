<?php

namespace tiFy\Template\Templates\ListTable\Items;

use tiFy\Kernel\Collection\QueryCollection;
use tiFy\Template\Templates\ListTable\Contracts\Collection as CollectionContract;
use tiFy\Template\Templates\ListTable\Contracts\Item;
use tiFy\Template\Templates\ListTable\Contracts\ListTable;

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
    protected $template;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items Liste des éléments.
     * @param ListTable $template Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($items, ListTable $template)
    {
        $this->template = $template;

        array_walk($items, [$this, 'wrap']);
    }

    /**
     * {@inheritdoc}
     */
    public function query($args = [])
    {
        if ($db = $this->template->db()) :
            $this->items = [];

            $query = $db->query(
                array_merge(
                    $this->template->request()->getQueryArgs(),
                    $args
                )
            );

            if ($items = $query->getItems()) :
                array_walk($items, [$this, 'wrap']);
            endif;

            $this->setFounds($query->getFoundItems());
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function wrap($item, $key = null)
    {
        return $this->items[$key] = $this->template->resolve('item', [$item, $this->template]);
    }
}