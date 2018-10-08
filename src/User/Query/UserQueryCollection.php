<?php

namespace tiFy\User\Query;

use Illuminate\Support\Collection;
use tiFy\Contracts\User\UserQueryCollectionInterface;
use tiFy\Contracts\User\UserQueryItemInterface;

class UserQueryCollection extends Collection implements UserQueryCollectionInterface
{
    /**
     * CONSTRUCTEUR
     *
     * @param UserQueryItemInterface[] $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}