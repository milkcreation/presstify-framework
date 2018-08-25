<?php

namespace tiFy\Query\Controller;

use Illuminate\Support\Collection;
use tiFy\App\AppTrait;

abstract class AbstractUserList extends Collection implements UserListInterface
{
    use AppTrait;

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