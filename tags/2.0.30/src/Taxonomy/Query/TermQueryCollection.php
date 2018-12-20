<?php

namespace tiFy\Taxonomy\Query;

use tiFy\Contracts\Taxonomy\TermQueryCollection as TermQueryCollectionContract;
use tiFy\Kernel\Collection\QueryCollection;
use WP_Term_Query;

class TermQueryCollection extends QueryCollection implements TermQueryCollectionContract
{
    /**
     * Liste des éléments déclarés.
     * @var TermQueryItem[] $items
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array|WP_Term_Query $items
     *
     * @return void
     */
    public function __construct($items)
    {
        if ($items instanceof WP_Term_Query) :
            if ($items->terms) :
                foreach($items->terms as $k => $term) :
                    $this->wrap($k, $term);
                endforeach;
            endif;
        else :
            $this->items = $items;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        return $this->collect()->pluck('term_id')->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getNames()
    {
        return $this->collect()->pluck('name')->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlugs()
    {
        return $this->collect()->pluck('slug')->all();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $key Clé d'indice de l'élément
     * @param \WP_Term $term
     *
     * @return TermQueryItem
     */
    public function wrap($key, $term)
    {
        return $this->items[$key] = new TermQueryItem($term);
    }
}