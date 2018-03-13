<?php

namespace tiFy\Core\Query\Controller;

use Illuminate\Support\Collection;
use tiFy\App\Traits\App as TraitsApp;

abstract class AbstractUserList extends Collection
{
    use TraitsApp;

    /**
     * CONSTRUCTEUR
     *
     * @param UserItemInterface[] $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}