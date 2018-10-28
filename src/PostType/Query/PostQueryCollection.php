<?php

namespace tiFy\PostType\Query;

use Illuminate\Support\Collection;
use tiFy\Contracts\PostType\PostQueryItemInterface;
use tiFy\Contracts\PostType\PostQueryCollectionInterface;

class PostQueryCollection extends Collection implements PostQueryCollectionInterface
{
    /**
     * CONSTRUCTEUR.
     *
     * @param PostQueryItemInterface[] $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}