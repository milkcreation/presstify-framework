<?php

namespace tiFy\Taxonomy\Query;

use Illuminate\Support\Collection;
use tiFy\Contracts\Taxonomy\TermQueryItemInterface;
use tiFy\Contracts\Taxonomy\TermQueryCollectionInterface;

class TermQueryCollection extends Collection implements TermQueryCollectionInterface
{
    /**
     * CONSTRUCTEUR.
     *
     * @param TermQueryItemInterface[] $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}