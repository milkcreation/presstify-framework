<?php

namespace tiFy\Query\Controller;

use Illuminate\Support\Collection;
use tiFy\Apps\AppTrait;

abstract class AbstractTermList extends Collection implements TermListInterface
{
    use AppTrait;

    /**
     * CONSTRUCTEUR
     *
     * @param TermItemInterface[] $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}