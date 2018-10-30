<?php

namespace tiFy\PostType\Query;

use Illuminate\Support\Collection;
use tiFy\Contracts\PostType\PostQueryItem;
use tiFy\Contracts\PostType\PostQueryCollection as PostQueryCollectionContract;

class PostQueryCollection extends Collection implements PostQueryCollectionContract
{
    /**
     * CONSTRUCTEUR.
     *
     * @param PostQueryItem[] $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}