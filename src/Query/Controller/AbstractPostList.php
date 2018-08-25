<?php

namespace tiFy\Query\Controller;

use Illuminate\Support\Collection;
use tiFy\App\AppTrait;

abstract class AbstractPostList extends Collection implements PostListInterface
{
    use AppTrait;

    /**
     * CONSTRUCTEUR
     *
     * @param PostItemInterface[] $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}