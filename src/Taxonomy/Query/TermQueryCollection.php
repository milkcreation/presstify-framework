<?php

namespace tiFy\Taxonomy\Query;

use Illuminate\Support\Collection;
use tiFy\Contracts\Taxonomy\TermQueryItem;
use tiFy\Contracts\Taxonomy\TermQueryCollection as TermQueryCollectionContract;

class TermQueryCollection extends Collection implements TermQueryCollectionContract
{
    /**
     * CONSTRUCTEUR.
     *
     * @param TermQueryItem[] $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}