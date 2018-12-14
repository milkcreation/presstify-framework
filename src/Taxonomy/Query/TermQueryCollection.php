<?php

namespace tiFy\Taxonomy\Query;

use tiFy\Contracts\Taxonomy\TermQueryItem;
use tiFy\Contracts\Taxonomy\TermQueryCollection as TermQueryCollectionContract;
use tiFy\Kernel\Collection\QueryCollection;

class TermQueryCollection extends QueryCollection implements TermQueryCollectionContract
{
    /**
     * Liste des éléments déclarés.
     * @var TermQueryItem[] $items
     */
    protected $items = [];

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
}